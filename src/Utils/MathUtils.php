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
}
