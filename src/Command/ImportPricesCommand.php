<?php

namespace App\Command;

use App\Entity\ItemPrice;
use App\Repository\ItemPriceRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import:prices',
    description: 'Imports prices for all items into the database',
)]
class ImportPricesCommand extends Command
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private ItemPriceRepository $itemPriceRepository
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Removing existing item price data');
        $this->itemPriceRepository->truncate();

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


        $entities = [];
        foreach ($itemsFromApi as $item) {
            $id = $item['id'] ?? null;
            $name = $item['name'] ?? null;
            $examine = $item['examine'] ?? null;
            if (!isset($id, $name, $examine)) {
                $io->warning('Skipping item due to missing data');
                continue;
            }

            $price = $pricesFromApi[$id] ?? null;
            $lowPrice = $price['low'] ?? null;
            $highPrice = $price['high'] ?? null;
            if (!isset($price, $lowPrice, $highPrice)) {
                $io->warning('Skipping item price due to missing data');
                continue;
            }

            $avgPrice = intval(round(($lowPrice + $highPrice) / 2));
            $entity = new ItemPrice();
            $entity->setName($name);
            $entity->setExamine($examine);
            $entity->setAvgPrice($avgPrice);
            $entity->setLowPrice($lowPrice);
            $entity->setHighPrice($highPrice);
            $entities[] = $entity;
        }

        $io->info('Saving latest item price data');
        $this->itemPriceRepository->save($entities);

        $io->success(sprintf('Successfully imported %s item prices', count($entities)));

        return Command::SUCCESS;
    }
}
