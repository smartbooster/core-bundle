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

    /**
     * @dataProvider formatBytesProvider
     */
    public function testFormatBytes(string $expected, float $size, ?int $roundPrecision = null): void
    {
        if ($roundPrecision === null) {
            $this->assertEquals($expected, MathUtils::formatBytes($size));
        } else {
            $this->assertEquals($expected, MathUtils::formatBytes($size, $roundPrecision));
        }
    }

    public function formatBytesProvider(): array
    {
        return [
            '999 B' => [
                // expected
                '999 B',
                // size
                999,
                // precision
                null,
            ],
            '1.29 KB' => ['1.29 KB', 1290, null],
            '562.999 MB' => ['562.999 MB', 562999000, 3],
            '945 GB' => ['945 GB', 945000000000, 0],
            '420.96 TB' => ['420.96 TB', 420956000000000, 2],
        ];
    }
}
