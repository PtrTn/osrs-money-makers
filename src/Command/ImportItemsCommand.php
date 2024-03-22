<?php

namespace App\Command;

use App\Entity\Item;
use App\Repository\ItemRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import:items',
    description: 'Imports all items and their ids into the database',
)]
class ImportItemsCommand extends Command
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private ItemRepository $itemRepository
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Removing existing item data');
        $this->itemRepository->truncate();

        $io->info('Fetching new item data');
        $data = $this->httpClient->request(
            'GET',
            'https://prices.runescape.wiki/api/v1/osrs/mapping',
        );
        $itemsFromApi = $data->toArray();
        $itemEntities = [];
        foreach ($itemsFromApi as $item) {
            if (!isset($item['id'], $item['name'], $item['examine'])) {
                $io->warning('Skipping item due to missing data');
                continue;
            }

            $entity = new Item();
            $entity->setItemId($item['id']);
            $entity->setName($item['name']);
            $entity->setDescription($item['examine']);
            $itemEntities[] = $entity;
        }

        $io->info('Saving new item data');
        $this->itemRepository->save($itemEntities);

        $io->success(sprintf('Successfully imported %s items', count($itemEntities)));

        return Command::SUCCESS;
    }
}
