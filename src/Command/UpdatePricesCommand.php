<?php

namespace App\Command;

use App\Entity\Item;
use App\Repository\ItemRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:update:prices',
    description: 'Updates prices for all items into the database',
)]
class UpdatePricesCommand extends Command
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private ItemRepository      $itemRepository,
        private LoggerInterface     $logger
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Fetching latest item price data');
        $priceData = $this->httpClient->request(
            'GET',
            'https://prices.runescape.wiki/api/v1/osrs/latest',
        );
        $pricesFromApi = $priceData->toArray()['data'] ?? [];

        $io->info('Fetching item data');
        $itemData = $this->httpClient->request(
            'GET',
            'https://prices.runescape.wiki/api/v1/osrs/mapping',
        );
        $itemsFromApi = $itemData->toArray();

        $io->info('Fetching existing item price data');
        $itemsFromDatabase = $this->itemRepository->findAllAsCollection();
        $inserts = 0;
        $updates = 0;
        $skipped = 0;

        $entities = [];
        foreach ($itemsFromApi as $item) {
            $itemId = $item['id'] ?? null;
            $name = $item['name'] ?? null;
            $examine = $item['examine'] ?? null;
            if (!isset($itemId, $name, $examine)) {
                $skipped++;
                $this->logger->warning('Skipping item due to missing data', $item);
                continue;
            }

            $price = $pricesFromApi[$itemId] ?? null;
            $lowPrice = $price['low'] ?? null;
            $highPrice = $price['high'] ?? null;
            if (!isset($price, $lowPrice, $highPrice)) {
                $skipped++;
                $this->logger->warning('Skipping item price due to missing data', ['id' => $itemId, 'price' => $pricesFromApi[$itemId] ?? null]);
                continue;
            }

            $entity = $itemsFromDatabase->findFirst(fn(int $id, Item $item) => $item->getGameId() === $itemId);
            if ($entity === null) {
                $inserts++;
                $entity = new Item();
            }
            else {
                $updates++;
            }
            $avgPrice = intval(round(($lowPrice + $highPrice) / 2));
            $entity->setGameId($itemId);
            $entity->setName($name);
            $entity->setExamine($examine);
            $entity->setAvgPrice($avgPrice);
            $entity->setLowPrice($lowPrice);
            $entity->setHighPrice($highPrice);
            $entities[] = $entity;
        }

        $io->info('Saving latest item price data');
        $this->itemRepository->save($entities);

        $io->success(sprintf('Successfully added %s items and updated %s items. Skipped %s items due to errors', $inserts, $updates, $skipped));

        return Command::SUCCESS;
    }
}
