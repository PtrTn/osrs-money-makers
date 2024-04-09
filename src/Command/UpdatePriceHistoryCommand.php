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
        $gameIds = [];
        foreach ($recipes as $recipe) {
            foreach ($recipe->getIngredients() as $ingredient) {
                $gameIds[] = $ingredient->getItem()->getGameId();
            }
            $gameIds[] = $recipe->getDish()->getItem()->getGameId();
        }
        $gameIds = array_unique($gameIds);

        dd($gameIds);
        // fetch timeseries 1h interval per item
        // create Price per internal
        // save Price and Item


        // todo, use historical data in FE

        return Command::SUCCESS;
    }
}
