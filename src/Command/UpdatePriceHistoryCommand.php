<?php

namespace App\Command;

use App\Calculation\DataSmoother;
use App\Entity\Item;
use App\Repository\ItemRepository;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

#[AsCommand(
    name: 'app:update:history',
    description: 'Imports all prices the database',
)]
class UpdatePriceHistoryCommand extends Command
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private RecipeRepository $recipeRepository,
        private ItemRepository $itemRepository,
        private LoggerInterface $logger,
        private DataSmoother $dataSmoother,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Fetching recipe data from database');
        $recipes = $this->recipeRepository->findAll();
        /** @var Item[] $itemsToUpdate */
        $itemsToUpdate = [];
        foreach ($recipes as $recipe) {
            foreach ($recipe->getIngredients() as $ingredient) {
                $itemsToUpdate[] = $ingredient->getItem();
             }
            $itemsToUpdate[] = $recipe->getDish()->getItem();
        }
        $itemsToUpdate = array_unique($itemsToUpdate);
        $itemsToUpdate = array_filter($itemsToUpdate, fn (Item $item) => !$item->isMoney());

        $io->info(sprintf('Fetching price history for %s items', count($itemsToUpdate)));
        $updates = 0;
        $skipped = 0;
        foreach ($itemsToUpdate as $item) {
            $priceData = $this->httpClient->request(
                'GET',
                'https://prices.runescape.wiki/api/v1/osrs/timeseries?timestep=1h&id=' . $item->getGameId(),
            );
            try {
                $pricesPerTimestamp = $priceData->toArray()['data'] ?? [];
            } catch (Exception | Throwable $exception) {
                $this->logger->warning('Unable to fetch price history for item', ['itemGameId' => $item->getGameId(), 'exception' => $exception]);
                $skipped++;
                continue;
            }

            $highPricesForLastWeek = [];
            $lowPricesForLastWeek = [];
            $highVolumeForLastWeek = [];
            $lowVolumeForLastWeek = [];
            foreach ($pricesPerTimestamp as $price) {
                if (!isset($price['timestamp'], $price['avgHighPrice'], $price['avgLowPrice'], $price['highPriceVolume'], $price['lowPriceVolume'])) {
                    $this->logger->warning('Incomplete item price history data', ['itemGameId' => $item->getGameId(), 'data' => $price]);
                    continue;
                }

                $priceDateTime = DateTimeImmutable::createFromFormat('U', $price['timestamp']);
                $lastWeek = new DateTimeImmutable('-8 days');
                if ($priceDateTime > $lastWeek) {
                    $highPricesForLastWeek[] = $price['avgHighPrice'];
                    $lowPricesForLastWeek[] = $price['avgLowPrice'];
                    $highVolumeForLastWeek[] = $price['highPriceVolume'];
                    $lowVolumeForLastWeek[] = $price['lowPriceVolume'];
                }
            }

            if (count($highPricesForLastWeek) === 0 || count($lowPricesForLastWeek) === 0) {
                $this->logger->info('No item price history data for last week', ['itemGameId' => $item->getGameId()]);
                $skipped++;
                continue;
            }

            $highPricesForLastWeek = $this->dataSmoother->smooth($highPricesForLastWeek);
            $lowPricesForLastWeek = $this->dataSmoother->smooth($lowPricesForLastWeek);

            $price = $item->getPrice();
            $price->setMaxHigh(max($highPricesForLastWeek));
            $price->setMinHigh(min($highPricesForLastWeek));
            $price->setMaxLow(max($lowPricesForLastWeek));
            $price->setMinLow(min($lowPricesForLastWeek));
            $price->setHighVolume(array_sum($highVolumeForLastWeek));
            $price->setLowVolume(array_sum($lowVolumeForLastWeek));

            $updates++;
        }

        $this->itemRepository->save($itemsToUpdate);

        $io->success(sprintf('Successfully updated %s historic prices. Skipped %s prices due to errors', $updates, $skipped));

        return Command::SUCCESS;
    }
}
