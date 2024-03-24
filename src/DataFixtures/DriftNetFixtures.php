<?php

namespace App\DataFixtures;

use App\Entity\ItemPrice;
use App\Entity\Recipe;
use App\Entity\Stack;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DriftNetFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $juteFibreItem = new ItemPrice();
        $juteFibreItem->setItemId(5931);
        $juteFibreItem->setName('Jute fibre');
        $juteFibreItem->setExamine('I can weave this to make sacks.');
        $juteFibreItem->setLowPrice(1200);
        $juteFibreItem->setHighPrice(1250);
        $juteFibreItem->setAvgPrice(1225);

        $driftNetItem = new ItemPrice();
        $driftNetItem->setItemId(21652);
        $driftNetItem->setName('Drift net');
        $driftNetItem->setExamine('A very large net, too big to be used by hand.');
        $driftNetItem->setLowPrice(2800);
        $driftNetItem->setHighPrice(3200);
        $driftNetItem->setAvgPrice(3000);

        $driftNetStack = new Stack();
        $driftNetStack->setQuantity(1);
        $driftNetStack->setItemPrice($driftNetItem);

        $juteFibreStack = new Stack();
        $juteFibreStack->setQuantity(2);
        $juteFibreStack->setItemPrice($juteFibreItem);

        $driftNetRecipe = new Recipe();
        $driftNetRecipe->setName('Drift nets');
        $driftNetRecipe->setThroughput(1100);
        $driftNetRecipe->setDish($driftNetStack);
        $driftNetRecipe->addIngredient($juteFibreStack);

         $manager->persist($driftNetRecipe);

        $manager->flush();
    }
}
