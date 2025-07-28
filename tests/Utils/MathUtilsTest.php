<?php

namespace Smart\CoreBundle\Tests\Utils;

use App\Utils\NumberUtils;
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

    public static function getPercentProvider(): array
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

    public static function formatBytesProvider(): array
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

    /** @dataProvider convertCentsToEuroProvider */
    public function testConvertCentsToEuro(float $expected, float $price): void
    {
        $this->assertSame($expected, MathUtils::convertCentsToEuro($price));
    }

    public static function convertCentsToEuroProvider(): array
    {
        return [
            'simple' => [5, 500],
            'float' => [5.54, 554],
            'little' => [0.01, 1],
            'price_float' => [5.893121, 589.3121],
        ];
    }

    /**
     * @dataProvider calculateAverageProvider
     */
    public function testCalculateAverage(float $expected, array $values, ?int $roundPrecision): void
    {
        if (null === $roundPrecision) {
            $result = MathUtils::calculateAverage($values);
        } else {
            $result = MathUtils::calculateAverage($values, $roundPrecision);
        }

        $this->assertEquals($expected, $result);
    }

    public static function calculateAverageProvider(): array
    {
        return [
            'empty values' => [0, [], null],
            'simple 1' => [50, [25, 50, 75], null],
            'simple 2' => [19, [10, 10, 20, 35], null],
            'float result' => [3.33, [5, 4, 1], 2]
        ];
    }

    /**
     * @dataProvider getCalculateDivisionProvider
     */
    public function testCalculateDivision(float $expected, ?int $dividend, int $divider, int $roundPrecision): void
    {
        $this->assertEquals($expected, MathUtils::calculateDivision($dividend, $divider, $roundPrecision));
    }

    public static function getCalculateDivisionProvider(): array
    {
        return [
            'simple' => [
                // expected
                2,
                // dividend
                10,
                // divider
                5,
                // round precision
                2
            ],
            'dividend null' => [
                // expected
                0,
                // dividend
                null,
                // divider
                10,
                // round precision
                2
            ],
            'zero' => [
                // expected
                0,
                // dividend
                10,
                // divider
                0,
                // round precision
                2
            ],
            'float' => [
                // expected
                3.333,
                // dividend
                10,
                // divider
                3,
                // round precision
                3
            ],
        ];
    }
}
