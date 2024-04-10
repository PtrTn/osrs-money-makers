<?php

namespace App\Tests\Entity;

use App\Entity\Price;
use PHPUnit\Framework\TestCase;

class PriceTests extends TestCase
{
    /**  @test */
    public function shouldCalculateBuyPrice(): void
    {
        $price = new Price();
        $price->setHigh(1303);
        $price->setMinHigh(1245);
        $price->setMaxHigh(1400);

        $this->assertSame('', $price->getBuyPriceStatus());
    }

    /**  @test */
    public function shouldCalculateSellPrice(): void
    {
        $price = new Price();
        $price->setLow(3600);
        $price->setMinLow(2800);
        $price->setMaxLow(3600);

        $this->assertSame('text-success', $price->getSellPriceStatus());
    }
}