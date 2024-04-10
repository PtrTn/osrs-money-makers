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
    name: 'app:update:items',
    description: 'Imports all items the database',
)]
class UpdateItemsCommand extends Command
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

        $io->info('Fetching item data');
        $itemData = $this->httpClient->request(
            'GET',
            'https://prices.runescape.wiki/api/v1/osrs/mapping',
        );
        $itemsFromApi = $itemData->toArray();

        $io->info('Fetching existing item data');
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

            $entity = $itemsFromDatabase->findFirst(fn(int $id, Item $item) => $item->getGameId() === $itemId);
            if ($entity === null) {
                $inserts++;
                $entity = new Item();
            }
            else {
                $updates++;
            }
            $entity->setGameId($itemId);
            $entity->setName($name);
            $entity->setExamine($examine);
            $entities[] = $entity;
        }

        $coinsExist = $itemsFromDatabase->exists(fn(int $id, Item $item) => $item->getGameId() === 617);
        if (!$coinsExist) {
            $coins = new Item();
            $coins->setName('Coins');
            $coins->setGameId(617);
            $coins->setExamine('Lovely money!');
            $entities[] = $coins;
        }

        $io->info('Saving latest item data');
        $this->itemRepository->save($entities);

        $io->success(sprintf('Successfully added %s items and updated %s items. Skipped %s items due to errors', $inserts, $updates, $skipped));

        return Command::SUCCESS;
    }
}
