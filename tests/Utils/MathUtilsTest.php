<?php

namespace Smart\CoreBundle\Tests\Utils;

use Smart\CoreBundle\Utils\MathUtils;
use PHPUnit\Framework\TestCase;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 *
 * vendor/bin/simple-phpunit tests/Utils/MathUtilsTest.php
 */
class MathUtilsTest extends TestCase
{
    /**
     * @dataProvider getPercentProvider
     */
    public function testCalcPercent(float $expected, float $partial, float $total, ?int $roundPrecision = null): void
    {
        $this->assertEqualsWithDelta($expected, MathUtils::calculatePercentage($partial, $total, $roundPrecision), 0.000000001);
    }

    public function getPercentProvider(): array
    {
        return [
            'calc_percent_round_0' => [
                // expected
                33,
                // partial
                3,
                // total
                9,
                // precision
                0,
            ],
            'calc_percent_without_rounding' => [33.333333333, 3, 9],
            'calc_percent_round_2' => [33.33, 3, 9, 2],
            'safe_calc_percent_divided_by_zero' => [0, 10, 0, 1],
        ];
    }
}
