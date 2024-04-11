<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\Stack;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $recipes[] = $this->createRecipe(['Jute fibre' => 2], 'Drift net', 1, 1100);
        $recipes[] = $this->createRecipe(['Soft clay' => 1, 'Law rune' => 1], 'Varrock teleport (tablet)', 1, 1100);
        $recipes[] = $this->createRecipe(['Soft clay' => 1, 'Law rune' => 1], 'Teleport to house (tablet)', 1, 1100);
        $recipes[] = $this->createRecipe(['Soft clay' => 1, 'Law rune' => 3, 'Astral rune' =>  3], 'Catherby teleport (tablet)', 1, 1100);
        $recipes[] = $this->createRecipe(['Steel bar' => 1], 'Cannonball', 4, 1080);
        $recipes[] = $this->createRecipe(
            [
                'Mahogany logs' => 1,
                'Nature rune' => 1,
                'Astral rune' => 2,
                'Coins' => 1050,
            ],
            'Mahogany plank',
            1,
            960
        );
        $recipes[] = $this->createRecipe(
            [
                'Ruby dragon bolts' => 10,
                'Cosmic rune' => 1,
                'Blood rune' => 1,
            ],
            'Ruby dragon bolts (e)',
            10,
            5500
        );
        $recipes[] = $this->createRecipe(
            [
                'Dragonstone' => 1,
                'Gold bar' => 1,
            ],
            'Dragonstone bracelet',
            1,
            1200
        );
        $recipes[] = $this->createRecipe(
            [
                'Dragonstone bracelet' => 1,
                'Cosmic rune' => 1,
            ],
            'Combat bracelet',
            1,
            1600
        );
        $recipes[] = $this->createRecipe(
            [
                'Clockwork' => 1,
                'Yew logs' => 1,
            ],
            'Yew bird house',
            1,
            2500
        );
        $recipes[] = $this->createRecipe(
            [
                'Clockwork' => 1,
                'Mahogany logs' => 1,
            ],
            'Mahogany bird house',
            1,
            2500
        );

        foreach ($recipes as $recipe) {
            $manager->persist($recipe);
        }

        $manager->flush();
    }

    public function createRecipe(array $inputItems, string $outputItem, int $outputQuantity, int $throughput): Recipe
    {
        $recipe = new Recipe();
        $recipe->setThroughput($throughput);
        foreach ($inputItems as $name => $quantity) {
            $stack = new Stack();
            $stack->setQuantity($quantity);
            $stack->setItem($this->getReference($name));
            $recipe->addIngredient($stack);
        }
        $dish = new Stack();
        $dish->setQuantity($outputQuantity);
        $dish->setItem($this->getReference($outputItem));
        $recipe->setDish($dish);

        return $recipe;
    }

    public function getDependencies(): array
    {
        return [ItemFixtures::class];
    }
}
