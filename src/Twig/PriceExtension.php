<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PriceExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
        ];
    }

    public function formatPrice(float $number, int $decimals = 1): string
    {
        if ($number > 1000000 || $number < -1000000) {
            return round($number / 1000000, $decimals) . 'm';
        }

        if ($number > 1000 || $number < -1000) {
            return round($number / 1000, $decimals) . 'k';
        }

        return round($number, $decimals);
    }
}