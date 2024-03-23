<?php

namespace App\Command;

use App\Entity\ItemPrice;
use App\Repository\ItemPriceRepository;
use App\Repository\ItemRepository;
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
        $data = $this->httpClient->request(
            'GET',
            'https://prices.runescape.wiki/api/v1/osrs/latest',
        );
        $pricesFromApi = $data->toArray()['data'] ?? [];
        $priceEntities = [];
        foreach ($pricesFromApi as $itemId => $itemPrice) {
            if (!isset($itemPrice['low'], $itemPrice['high'])) {
                $io->warning('Skipping item price due to missing data');
                continue;
            }

            $avgPrice = intval(round(($itemPrice['low'] + $itemPrice['high']) / 2));
            $entity = new ItemPrice();
            $entity->setAvgPrice($avgPrice);
            $entity->setLowPrice($itemPrice['low']);
            $entity->setHighPrice($itemPrice['high']);
            $entity->setItemId($itemId);
            $priceEntities[] = $entity;
        }

        $io->info('Saving latest item price data');
        $this->itemPriceRepository->save($priceEntities);

        $io->success(sprintf('Successfully imported %s item prices', count($priceEntities)));

        return Command::SUCCESS;
    }
}
