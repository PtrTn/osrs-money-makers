<?php

namespace App\Command;

use App\Entity\Item;
use App\Entity\Price;
use App\Entity\Recipe;
use App\Entity\Stack;
use App\Repository\ItemRepository;
use App\Repository\PriceRepository;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:update:history',
    description: 'Imports all prices the database',
)]
class UpdatePriceHistoryCommand extends Command
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private RecipeRepository $recipeRepository,
        private LoggerInterface     $logger
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Fetching recipe data from database');
        $recipes = $this->recipeRepository->findAll();
        $itemGameIds = [];
        foreach ($recipes as $recipe) {
            foreach ($recipe->getIngredients() as $ingredient) {
                $itemGameIds[] = $ingredient->getItem()->getGameId();
                         }
            $itemGameIds[] = $recipe->getDish()->getItem()->getGameId();
        }
        $itemGameIds = array_unique($itemGameIds);

        $io->info(sprintf('Fetching price history for %s items', count($itemGameIds)));
        foreach (array_slice($itemGameIds, 0, 1) as $itemGameId) {
            $priceData = $this->httpClient->request(
                'GET',
                'https://prices.runescape.wiki/api/v1/osrs/timeseries?timestep=5m&id=' . $itemGameId,
            );
            try {
                $pricesPerTimestamp = $priceData->toArray()['data'] ?? [];
            } catch (Exception $exception) {
                $this->logger->warning('Unable to fetch price history for item', ['itemGameId' => $itemGameId, 'exception' => $exception]);
            }

            $pricesForLastWeek = [];
            foreach ($pricesPerTimestamp as $price) {
                if (!isset($price['timestamp'], $price['avgHighPrice'], $price['avgLowPrice'])) {
                    $this->logger->warning('Incomplete item price history data');
                    continue;
                }

                $priceDateTime = DateTimeImmutable::createFromFormat('U', $price['timestamp']);
                $lastWeek = new DateTimeImmutable('-1 week');
                if ($priceDateTime > $lastWeek) {
                    $pricesForLastWeek[] = $price['avgHighPrice'];
                    $pricesForLastWeek[] = $price['avgLowPrice'];
                }
            }

            dd(min($pricesForLastWeek), max($pricesForLastWeek));
        }
        // fetch timeseries 1h interval per item
        // create Price per internal
        // save Price and Item


        // todo, use historical data in FE

        return Command::SUCCESS;
    }
}
