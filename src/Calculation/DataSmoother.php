<?php

namespace App\Calculation;

use HiFolks\Statistics\Stat;

class DataSmoother
{
    /**
     * Removes any outliers (more than 2 standard deviations from the mean) from a list of numbers
     * @param int[] $data
     */
    public function smooth(array $data): array
    {
        $mean = Stat::mean($data);
        $standardDeviation = Stat::pstdev($data);
        $cutoff = 2 * $standardDeviation;

        return array_filter($data, function (int $dataPoint) use ($mean, $cutoff) {
            $deviation = abs($dataPoint - $mean);

            return $deviation < $cutoff;
        });
    }
}