<?php

namespace App\Command;

use App\Entity\Item;
use App\Entity\Price;
use App\Repository\ItemRepository;
use App\Repository\PriceRepository;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:update:prices',
    description: 'Imports all prices the database',
)]
class UpdatePricesCommand extends Command
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private PriceRepository     $priceRepository,
        private ItemRepository      $itemRepository,
        private LoggerInterface     $logger
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Fetching price data from Runescape wiki');
        $priceData = $this->httpClient->request(
            'GET',
            'https://prices.runescape.wiki/api/v1/osrs/latest',
        );
        $pricesFromApi = $priceData->toArray()['data'] ?? [];

        $io->info('Fetching item data from database');
        $itemsFromDatabase = $this->itemRepository->findAllAsCollection();

        $inserts = 0;
        $updates = 0;
        $skipped = 0;
        $entities = [];
        foreach ($pricesFromApi as $itemGameId => $price) {
            $lowPrice = $price['low'] ?? null;
            $highPrice = $price['high'] ?? null;
            if (!isset($price, $lowPrice, $highPrice)) {
                $skipped++;
                $this->logger->warning('Skipping item price due to missing data', ['itemGameId' => $itemGameId, 'price' => $price ?? null]);
                continue;
            }

            $itemEntity = $itemsFromDatabase->findFirst(fn(int $id, Item $item) => $item->getGameId() === $itemGameId);
            if ($itemEntity === null) {
                $skipped++;
                $this->logger->warning('Skipping item price as it cant be mapped to an item', ['itemGameId' => $itemGameId]);
                continue;
            }

            $priceEntity = $itemEntity->getPrice();
            if ($priceEntity === null) {
                $inserts++;
                $priceEntity = new Price();
            }
            else {
                $updates++;
            }
            $priceEntity->setHigh($highPrice);
            $priceEntity->setLow($lowPrice);
            $itemEntity->setPrice($priceEntity);

            $entities[] = $itemEntity;
        }

        $io->info('Saving latest item price data');
        $this->priceRepository->save($entities);

        // todo, save last update date (and show with time ago)

        $io->success(sprintf('Successfully added %s prices and updated %s prices. Skipped %s prices due to errors', $inserts, $updates, $skipped));

        return Command::SUCCESS;
    }
}
