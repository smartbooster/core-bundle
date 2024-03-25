<?php

namespace Smart\CoreBundle\Utils;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class MathUtils
{
    public static function calculatePercentage(?float $partial, float $total, ?int $roundPrecision = null): float
    {
        if ($total == 0 || $partial === null) {
            return 0;
        }

        $toReturn = $partial * 100 / $total;

        return $roundPrecision === null ? $toReturn : round($toReturn, $roundPrecision);
    }

    /**
     * Convert and Transform byte into readable format
     * For a better understanding on Byte conversion check this link : https://www.techtarget.com/searchstorage/definition/kilobyte#:~:text=Originally%2C%20a%20byte%20was%20considered,decimal%20form%2C%201%2C024%20bytes).
     */
    public static function formatBytes(float $size, int $precision = 2): string
    {
        $base = log($size, 1000);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

        $floor = floor($base);
        return round(pow(1000, $base - $floor), $precision) . ' ' . $suffixes[$floor];
    }

    public static function convertCentsToEuro(?float $price): float
    {
        if (null === $price) {
            return 0.0;
        }
        return $price / 100;
    }

    /**
     * Calculate the average score of an array of values as float
     *
     * <pre>
     * <?php
     * calculateAverage([25, 50, 75]);
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * 50
     * </pre>
     */
    public static function calculateAverage(array $values, int $roundPrecision = 0): float
    {
        return empty($values) ? 0 : round(array_sum($values) / count($values), $roundPrecision);
    }

    public static function calculateDivision(int|float|null $dividend, int|float $divider, int $roundPrecision = 2): float
    {
        if (0 == $divider || null === $dividend) {
            return 0;
        }

        return round($dividend / $divider, $roundPrecision);
    }
}
