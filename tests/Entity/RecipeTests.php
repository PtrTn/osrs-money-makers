<?php

namespace App\Tests\Entity;

use App\Entity\Item;
use App\Entity\Price;
use App\Entity\Recipe;
use App\Entity\Stack;
use PHPUnit\Framework\TestCase;

class RecipeTests extends TestCase
{
    const BOLT_PRICE = 882;
    const COSMIC_PRICE = 86;
    const BLOOD_PRICE = 229;
    const BOLT_E_PRICE = 1026;
    const THROUGHPUT = 5500;
    const BOLT_QUANTITY = 10;
    const BOLT_E_QUANTITY = 10;

    /**
     * @test
     */
    public function shouldCalculateProfit(): void
    {
        $boltPrice = new Price();
        $boltPrice->setHigh(self::BOLT_PRICE);
        $boltItem = new Item();
        $boltItem->setPrice($boltPrice);
        $boltStack = new Stack();
        $boltStack->setItem($boltItem);
        $boltStack->setQuantity(self::BOLT_QUANTITY);

        $cosmicPrice = new Price();
        $cosmicPrice->setHigh(self::COSMIC_PRICE);
        $cosmicItem = new Item();
        $cosmicItem->setPrice($cosmicPrice);
        $cosmicStack = new Stack();
        $cosmicStack->setItem($cosmicItem);
        $cosmicStack->setQuantity(1);

        $bloodPrice = new Price();
        $bloodPrice->setHigh(self::BLOOD_PRICE);
        $bloodItem = new Item();
        $bloodItem->setPrice($bloodPrice);
        $bloodStack = new Stack();
        $bloodStack->setItem($bloodItem);
        $bloodStack->setQuantity(1);

        $boltEPrice = new Price();
        $boltEPrice->setLow(self::BOLT_E_PRICE);
        $boltEItem = new Item();
        $boltEItem->setPrice($boltEPrice);
        $boltEStack = new Stack();
        $boltEStack->setItem($boltEItem);
        $boltEStack->setQuantity(self::BOLT_E_QUANTITY);

        $recipe = new Recipe();
        $recipe->setThroughput(self::THROUGHPUT);
        $recipe->setDish($boltEStack);
        $recipe->addIngredient($boltStack);
        $recipe->addIngredient($cosmicStack);
        $recipe->addIngredient($bloodStack);

        $expectedInput = (self::BOLT_PRICE * self::BOLT_QUANTITY + self::COSMIC_PRICE + self::BLOOD_PRICE) * self::THROUGHPUT;
        $this->assertSame($expectedInput, $recipe->getInput());

        $expectedOutput = (self::BOLT_E_PRICE * self::BOLT_E_QUANTITY) * self::THROUGHPUT;
        $this->assertSame($expectedOutput, $recipe->getOutput());

        $expectedCut = (int) round($expectedOutput * 0.01);
        $this->assertSame($expectedCut, $recipe->getGrandExchangeCut());

        $expectedProfit = $expectedOutput - $expectedCut - $expectedInput;
        $this->assertSame($expectedProfit, $recipe->getAvgProfit());
    }
}