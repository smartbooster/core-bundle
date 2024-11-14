<?php

namespace Smart\CoreBundle\Tests\Utils;

use Smart\CoreBundle\Utils\DateUtils;
use PHPUnit\Framework\TestCase;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 *
 * vendor/bin/simple-phpunit tests/Utils/DateUtilsTest.php
 */
class DateUtilsTest extends TestCase
{
    /**
     * @dataProvider getMonthsBetweenProvider
     */
    public function testGetMonthsBetween(array $expected, \DateTime $startedAt, \DateTime $endedAt): void
    {
        $this->assertSame($expected, DateUtils::getMonthsBetween($startedAt, $endedAt));
    }

    public function getMonthsBetweenProvider(): array
    {
        return [
            'same year with 1 month' => [
                // expected
                [
                    '2022-09',
                ],
                // startedAt
                new \DateTime('2022-09-01'),
                // endedAt
                new \DateTime('2022-09-30'),
            ],
            'same year with 3 month' => [
                // expected
                [
                    '2022-08',
                    '2022-09',
                    '2022-10',
                ],
                // startedAt
                new \DateTime('2022-08-01'),
                // endedAt
                new \DateTime('2022-10-20'),
            ],
            "changing year 5 month before + 2 month after" => [
                // expected
                [
                    '2022-08',
                    '2022-09',
                    '2022-10',
                    '2022-11',
                    '2022-12',
                    '2023-01',
                    '2023-02',
                ],
                // startedAt
                new \DateTime('2022-08-01'),
                // endedAt
                new \DateTime('2023-02-28'),
            ],
        ];
    }

    /**
     * @dataProvider getFormattedDayOrMonthProvider
     */
    public function testGetFormattedDayOrMonth(string $expected, int $number): void
    {
        $this->assertEquals($expected, DateUtils::getFormattedDayOrMonth($number));
    }

    public function getFormattedDayOrMonthProvider(): array
    {
        return [
            '01' => ['01', 1],
            '09' => ['09', 9],
            '10' => ['10', 10],
        ];
    }

    public function testGetMonthChoices(): void
    {
        $this->assertEquals([
            // since all our project are in french, we prioritize our litteral test using the fr locale
            'Janvier' => 1,
            'Février' => 2,
            'Mars' => 3,
            'Avril' => 4,
            'Mai' => 5,
            'Juin' => 6,
            'Juillet' => 7,
            'Août' => 8,
            'Septembre' => 9,
            'Octobre' => 10,
            'Novembre' => 11,
            'Décembre' => 12,
        ], DateUtils::getMonthChoices());
    }

    public function testMonthYearToString(): void
    {
        $this->assertEquals('Janvier 2001', DateUtils::monthYearToString(1, 2001));
        $this->assertEquals('Juillet 2007', DateUtils::monthYearToString(7, 2007));
        $this->assertEquals('Décembre 2012', DateUtils::monthYearToString(12, 2012));
        $this->assertNull(DateUtils::monthYearToString(7));
        $this->assertNull(DateUtils::monthYearToString(year: 2023));
    }

    /**
     * @dataProvider getDaysBetweenProvider
     */
    public function testGetDaysBetween(array $expected, \DateTime $startedAt, \DateTime $endedAt, array $options = []): void
    {
        $this->assertSame($expected, DateUtils::getDaysBetween($startedAt, $endedAt, $options));
    }

    public function getDaysBetweenProvider(): array
    {
        return [
            'same month' => [
                // expected
                [
                    '2023-09-01',
                    '2023-09-02',
                    '2023-09-03',
                ],
                // startedAt
                new \DateTime('2023-09-01'),
                // endedAt
                new \DateTime('2023-09-03'),
            ],
            'changing month' => [
                [
                    '2023-02-28',
                    '2023-03-01',
                    '2023-03-02',
                ],
                new \DateTime('2023-02-28'),
                new \DateTime('2023-03-02'),
            ],
            "changing year" => [
                // expected
                [
                    '2022-12-30',
                    '2022-12-31',
                    '2023-01-01',
                ],
                // startedAt
                new \DateTime('2022-12-30'),
                // endedAt
                new \DateTime('2023-01-01'),
            ],
            "Multi-day period with Start Time > start_hour and End Time > end_hour" => [
                // expected
                [
                    '2022-12-31',
                    '2023-01-01',
                ],
                // startedAt
                (new \DateTime('2022-12-30'))->setTime(10, 1),
                // endedAt
                (new \DateTime('2023-01-01'))->setTime(10, 1),
                ['start_hour' => 10, 'end_hour' => 10]
            ],
            "Multi-day period with Start Time > start_hour and End Time < end_hour" => [
                // expected
                [
                    '2022-12-31'
                ],
                // startedAt
                (new \DateTime('2022-12-30'))->setTime(10, 1),
                // endedAt
                (new \DateTime('2023-01-01'))->setTime(9, 59),
                ['start_hour' => 10, 'end_hour' => 10]
            ],
            "Multi-day period with Start Time < start_hour and End Time > end_hour" => [
                // expected
                [
                    '2022-12-30',
                    '2022-12-31',
                    '2023-01-01',
                ],
                // startedAt
                (new \DateTime('2022-12-30'))->setTime(9, 59),
                // endedAt
                (new \DateTime('2023-01-01'))->setTime(10, 1),
                ['start_hour' => 10, 'end_hour' => 10]
            ],
            "Multi-day period with Start Time < start_hour and End Time < end_hour" => [
                // expected
                [
                    '2022-12-30',
                    '2022-12-31',
                ],
                // startedAt
                (new \DateTime('2022-12-30'))->setTime(9, 59),
                // endedAt
                (new \DateTime('2023-01-01'))->setTime(9, 59),
                ['start_hour' => 10, 'end_hour' => 10]
            ],
            "Single day period with start time < start_hour and end time > end_hour" => [
                // expected
                [
                    '2023-01-01'
                ],
                // startedAt
                (new \DateTime('2023-01-01'))->setTime(7, 0),
                // endedAt
                (new \DateTime('2023-01-01'))->setTime(19, 0),
                ['start_hour' => 7, 'end_hour' => 19]
            ],
        ];
    }

    /**
     * @dataProvider getTimeIntervalsProvider
     */
    public function testGetTimeIntervals(array $expected, string $startTime, string $endTime): void
    {
        $this->assertSame($expected, DateUtils::getTimeIntervals($startTime, $endTime));
    }

    public function getTimeIntervalsProvider(): array
    {
        return [
            'morning transition after noon' => [
                // expected
                [
                    "09:00",
                    "09:15",
                    "09:30",
                    "09:45",
                    "10:00",
                    "10:15",
                    "10:30",
                    "10:45",
                    "11:00",
                    "11:15",
                    "11:30",
                    "11:45",
                    "12:00",
                    "12:15",
                    "12:30",
                    "12:45",
                    "13:00",
                    "13:15",
                    "13:30",
                    "13:45",
                    "14:00",
                ],
                '09:00',
                '14:00',
            ],

        ];
    }

    /**
     * @dataProvider getCalendarDaysProvider
     */
    public function testGetCalendarDays(int $month, int $year, array $expected): void
    {
        $results = DateUtils::getCalendarDays($month, $year);
        $this->assertNotEmpty($results);
        $i = 0;
        foreach ($results as $key => $result) {
            $this->assertSame($key, (new \DateTime())->setDate($result['year'], $result['month'], $result['day'])->format('Y-m-d'));
            foreach (['day', 'month', 'year', 'isWeekend'] as $resultKey) {
                $this->assertSame($result[$resultKey], $expected[$i][$resultKey]);
            }
            $i++;
        }
    }

    public function getCalendarDaysProvider(): array
    {
        return [
            '01_2023' => [
                1,
                2023,
                [
                    ['day' => 26, 'month' => 12, 'year' => 2022, 'isWeekend' => false],
                    ['day' => 27, 'month' => 12, 'year' => 2022, 'isWeekend' => false],
                    ['day' => 28, 'month' => 12, 'year' => 2022, 'isWeekend' => false],
                    ['day' => 29, 'month' => 12, 'year' => 2022, 'isWeekend' => false],
                    ['day' => 30, 'month' => 12, 'year' => 2022, 'isWeekend' => false],
                    ['day' => 31, 'month' => 12, 'year' => 2022, 'isWeekend' => true],
                    ['day' => 1, 'month' => 1, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 2, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 3, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 4, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 5, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 6, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 7, 'month' => 1, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 8, 'month' => 1, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 9, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 10, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 11, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 12, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 13, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 14, 'month' => 1, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 15, 'month' => 1, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 16, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 17, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 18, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 19, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 20, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 21, 'month' => 1, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 22, 'month' => 1, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 23, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 24, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 25, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 26, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 27, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 1, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 29, 'month' => 1, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 30, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 31, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 3, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 4, 'month' => 2, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 5, 'month' => 2, 'year' => 2023, 'isWeekend' => true],
                ],
            ],
            '02_2023' => [
                2,
                2023,
                [
                    ['day' => 30, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 31, 'month' => 1, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 3, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 4, 'month' => 2, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 5, 'month' => 2, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 6, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 7, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 8, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 9, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 10, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 11, 'month' => 2, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 12, 'month' => 2, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 13, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 14, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 15, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 16, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 17, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 18, 'month' => 2, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 19, 'month' => 2, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 20, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 21, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 22, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 23, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 24, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 25, 'month' => 2, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 26, 'month' => 2, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 27, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 3, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 4, 'month' => 3, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 5, 'month' => 3, 'year' => 2023, 'isWeekend' => true],
                ],
            ],
            '03_2023' => [
                3,
                2023,
                [
                    ['day' => 27, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 2, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 3, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 4, 'month' => 3, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 5, 'month' => 3, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 6, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 7, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 8, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 9, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 10, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 11, 'month' => 3, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 12, 'month' => 3, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 13, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 14, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 15, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 16, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 17, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 18, 'month' => 3, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 19, 'month' => 3, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 20, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 21, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 22, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 23, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 24, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 25, 'month' => 3, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 26, 'month' => 3, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 27, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 29, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 30, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 31, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 4, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 2, 'month' => 4, 'year' => 2023, 'isWeekend' => true],
                ],
            ],
            '04_2023' => [
                4,
                2023,
                [
                    ['day' => 27, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 29, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 30, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 31, 'month' => 3, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 4, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 2, 'month' => 4, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 3, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 4, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 5, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 6, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 7, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 8, 'month' => 4, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 9, 'month' => 4, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 10, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 11, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 12, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 13, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 14, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 15, 'month' => 4, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 16, 'month' => 4, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 17, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 18, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 19, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 20, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 21, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 22, 'month' => 4, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 23, 'month' => 4, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 24, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 25, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 26, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 27, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 4, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 29, 'month' => 4, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 30, 'month' => 4, 'year' => 2023, 'isWeekend' => true],
                ],
            ],
            '05_2023' => [
                5,
                2023,
                [
                    ['day' => 1, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 3, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 4, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 5, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 6, 'month' => 5, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 7, 'month' => 5, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 8, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 9, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 10, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 11, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 12, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 13, 'month' => 5, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 14, 'month' => 5, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 15, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 16, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 17, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 18, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 19, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 20, 'month' => 5, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 21, 'month' => 5, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 22, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 23, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 24, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 25, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 26, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 27, 'month' => 5, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 28, 'month' => 5, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 29, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 30, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 31, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 3, 'month' => 6, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 4, 'month' => 6, 'year' => 2023, 'isWeekend' => true],
                ],
            ],
            '06_2023' => [
                6,
                2023,
                [
                    ['day' => 29, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 30, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 31, 'month' => 5, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 3, 'month' => 6, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 4, 'month' => 6, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 5, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 6, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 7, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 8, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 9, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 10, 'month' => 6, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 11, 'month' => 6, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 12, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 13, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 14, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 15, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 16, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 17, 'month' => 6, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 18, 'month' => 6, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 19, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 20, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 21, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 22, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 23, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 24, 'month' => 6, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 25, 'month' => 6, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 26, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 27, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 29, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 30, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 7, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 2, 'month' => 7, 'year' => 2023, 'isWeekend' => true],
                ],
            ],
            '07_2023' => [
                7,
                2023,
                [
                    ['day' => 26, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 27, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 29, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 30, 'month' => 6, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 7, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 2, 'month' => 7, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 3, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 4, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 5, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 6, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 7, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 8, 'month' => 7, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 9, 'month' => 7, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 10, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 11, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 12, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 13, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 14, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 15, 'month' => 7, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 16, 'month' => 7, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 17, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 18, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 19, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 20, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 21, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 22, 'month' => 7, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 23, 'month' => 7, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 24, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 25, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 26, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 27, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 29, 'month' => 7, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 30, 'month' => 7, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 31, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 3, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 4, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 5, 'month' => 8, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 6, 'month' => 8, 'year' => 2023, 'isWeekend' => true],
                ],
            ],
            '08_2023' => [
                8,
                2023,
                [
                    ['day' => 31, 'month' => 7, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 3, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 4, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 5, 'month' => 8, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 6, 'month' => 8, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 7, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 8, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 9, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 10, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 11, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 12, 'month' => 8, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 13, 'month' => 8, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 14, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 15, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 16, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 17, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 18, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 19, 'month' => 8, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 20, 'month' => 8, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 21, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 22, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 23, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 24, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 25, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 26, 'month' => 8, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 27, 'month' => 8, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 28, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 29, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 30, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 31, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 9, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 3, 'month' => 9, 'year' => 2023, 'isWeekend' => true],
                ],
            ],
            '09_2023' => [
                9,
                2023,
                [
                    ['day' => 28, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 29, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 30, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 31, 'month' => 8, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 9, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 3, 'month' => 9, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 4, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 5, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 6, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 7, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 8, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 9, 'month' => 9, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 10, 'month' => 9, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 11, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 12, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 13, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 14, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 15, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 16, 'month' => 9, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 17, 'month' => 9, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 18, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 19, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 20, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 21, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 22, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 23, 'month' => 9, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 24, 'month' => 9, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 25, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 26, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 27, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 29, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 30, 'month' => 9, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 1, 'month' => 10, 'year' => 2023, 'isWeekend' => true],
                ],
            ],
            '10_2023' => [
                10,
                2023,
                [
                    ['day' => 25, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 26, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 27, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 29, 'month' => 9, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 30, 'month' => 9, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 1, 'month' => 10, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 2, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 3, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 4, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 5, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 6, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 7, 'month' => 10, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 8, 'month' => 10, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 9, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 10, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 11, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 12, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 13, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 14, 'month' => 10, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 15, 'month' => 10, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 16, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 17, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 18, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 19, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 20, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 21, 'month' => 10, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 22, 'month' => 10, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 23, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 24, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 25, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 26, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 27, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 10, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 29, 'month' => 10, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 30, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 31, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 3, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 4, 'month' => 11, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 5, 'month' => 11, 'year' => 2023, 'isWeekend' => true],
                ],
            ],
            '11_2023' => [
                11,
                2023,
                [
                    ['day' => 30, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 31, 'month' => 10, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 3, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 4, 'month' => 11, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 5, 'month' => 11, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 6, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 7, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 8, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 9, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 10, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 11, 'month' => 11, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 12, 'month' => 11, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 13, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 14, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 15, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 16, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 17, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 18, 'month' => 11, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 19, 'month' => 11, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 20, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 21, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 22, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 23, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 24, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 25, 'month' => 11, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 26, 'month' => 11, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 27, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 29, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 30, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 12, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 3, 'month' => 12, 'year' => 2023, 'isWeekend' => true],
                ],
            ],
            '12_2023' => [
                12,
                2023,
                [
                    ['day' => 27, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 29, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 30, 'month' => 11, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 1, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 2, 'month' => 12, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 3, 'month' => 12, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 4, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 5, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 6, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 7, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 8, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 9, 'month' => 12, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 10, 'month' => 12, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 11, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 12, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 13, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 14, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 15, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 16, 'month' => 12, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 17, 'month' => 12, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 18, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 19, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 20, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 21, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 22, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 23, 'month' => 12, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 24, 'month' => 12, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 25, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 26, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 27, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 28, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 29, 'month' => 12, 'year' => 2023, 'isWeekend' => false],
                    ['day' => 30, 'month' => 12, 'year' => 2023, 'isWeekend' => true],
                    ['day' => 31, 'month' => 12, 'year' => 2023, 'isWeekend' => true],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getPrevIntFormatFromMonthYearProvider
     */
    public function testGetPrevIntFormatFromMonthYear(int $expected, int $month, int $year, string $format): void
    {
        $this->assertSame($expected, DateUtils::getPrevIntFormatFromMonthYear($month, $year, $format));
    }

    public function getPrevIntFormatFromMonthYearProvider(): array
    {
        return [
            '01_2023_n' => [12, 1, 2023, 'n'],
            '01_2023_Y' => [2022, 1, 2023, 'Y'],
            '12_2023_n' => [11, 12, 2023, 'n'],
            '12_2023_Y' => [2023, 12, 2023, 'Y'],
        ];
    }

    /**
     * @dataProvider getNextIntFormatFromMonthYearProvider
     */
    public function testGetNextIntFormatFromMonthYear(int $expected, int $month, int $year, string $format): void
    {
        $this->assertSame($expected, DateUtils::getNextIntFormatFromMonthYear($month, $year, $format));
    }

    public function getNextIntFormatFromMonthYearProvider(): array
    {
        return [
            '12_2023_n' => [1, 12, 2023, 'n'],
            '12_2023_Y' => [2024, 12, 2023, 'Y'],
            '11_2023_n' => [12, 11, 2023, 'n'],
            '11_2023_Y' => [2023, 11, 2023, 'Y'],
        ];
    }

    /**
     * @dataProvider getFirstDayOfMonthYearProvider
     */
    public function testGetFirstDayOfMonthYear(string $expected, int $month, int $year): void
    {
        $this->assertSame($expected, DateUtils::getFirstDayOfMonthYear($month, $year)->format('Y-m-d H:i:s'));
    }

    public function getFirstDayOfMonthYearProvider(): array
    {
        return [
            '2023-12-01 00:00:00' => ['2023-12-01 00:00:00', 12, 2023],
            '2024-01-01 00:00:00' => ['2024-01-01 00:00:00', 1, 2024],
        ];
    }

    /**
     * @dataProvider getLastDayOfMonthYearProvider
     */
    public function testGetLastDayOfMonthYear(string $expected, int $month, int $year): void
    {
        $this->assertSame($expected, DateUtils::getLastDayOfMonthYear($month, $year)->format('Y-m-d H:i:s'));
    }

    public function getLastDayOfMonthYearProvider(): array
    {
        return [
            '31_days' => ['2023-12-31 23:59:59', 12, 2023],
            '29_days' => ['2024-02-29 23:59:59', 2, 2024],
            '28_days' => ['2023-02-28 23:59:59', 2, 2023],
            '30_days' => ['2024-04-30 23:59:59', 4, 2024],
        ];
    }

    /**
     * @dataProvider getGetFirstDayYearFromDateTimeProvider
     */
    public function testGetFirstDayYearFromDateTime(\DateTime $expected, \DateTime $datetime): void
    {
        $this->assertEquals($expected, DateUtils::getFirstDayYearFromDateTime($datetime));
    }

    public function getGetFirstDayYearFromDateTimeProvider(): array
    {
        return [
            'septembre_2020' => [
                new \DateTime("2020-01-01"),
                new \DateTime("2020-09-18")
            ],
            'juillet_2019' => [
                new \DateTime("2019-01-01"),
                new \DateTime("2019-07-24")
            ],
        ];
    }

    /**
     * @dataProvider getGetLastDayMonthFromDateTimeProvider
     */
    public function testGetLastDayMonthFromDateTime(\DateTime $expected, \DateTime $datetime): void
    {
        $this->assertEquals($expected, DateUtils::getLastDayMonthFromDateTime($datetime));
    }

    public function getGetLastDayMonthFromDateTimeProvider(): array
    {
        return [
            'september_2020' => [
                new \DateTime("2020-09-30 23:59:59"),
                new \DateTime("2020-09-18")
            ],
            'october_2020' => [
                new \DateTime("2020-10-31 23:59:59"),
                new \DateTime("2020-10-07 10:42:30")
            ],
            'leap_february_2020' => [
                new \DateTime("2020-02-29 23:59:59"),
                new \DateTime("2020-02-12")
            ],
            'february_2019' => [
                new \DateTime("2019-02-28 23:59:59"),
                new \DateTime("2019-02-12")
            ],
        ];
    }

    /**
     * @dataProvider getMonthsBetweenDateTimesProvider
     */
    public function testGetMonthsBetweenDateTimes(array $expected, \DateTime $start, \DateTime $end): void
    {
        $this->assertEquals($expected, DateUtils::getMonthsBetweenDateTimes($start, $end));
    }

    public function getMonthsBetweenDateTimesProvider(): array
    {
        return [
            'one_month' => [
                ['2015-05'],
                new \DateTime('2015-05-14'),
                new \DateTime('2015-05-14'),
            ],
            'may_sept_2015' => [
                ['2015-05', '2015-06', '2015-07', '2015-08', '2015-09'],
                new \DateTime('2015-05-14'),
                new \DateTime('2015-09-02'),
            ],
            'dec_2019_may_2020' => [
                ['2019-12', '2020-01', '2020-02', '2020-03', '2020-04', '2020-05'],
                new \DateTime('2019-12-25'),
                new \DateTime('2020-05-10'),
            ],
            'start_after_end' => [
                ['2015-05', '2015-06', '2015-07', '2015-08', '2015-09'],
                new \DateTime('2015-09-30 23:59:59'),
                new \DateTime('2015-05-01 00:00:00'),
            ]
        ];
    }

    /**
     * @dataProvider getDateTimeMonthProvider
     */
    public function testGetDateTimeMonth(string $expected, \DateTime $value): void
    {
        $this->assertEquals($expected, DateUtils::getDateTimeMonth($value));
    }

    public function getDateTimeMonthProvider(): array
    {
        return [
            '02' => [
                // expected
                '02',
                // value
                new \DateTime('2022-02-05')
            ],
            '10' => [
                // expected
                '10',
                // value
                new \DateTime('2021-10-10')
            ],
        ];
    }

    /**
     * @dataProvider getDateTimeYearProvider
     */
    public function testGetDateTimeYear(string $expected, \DateTime $value): void
    {
        $this->assertEquals($expected, DateUtils::getDateTimeYear($value));
    }

    public function getDateTimeYearProvider(): array
    {
        return [
            '2022' => [
                // expected
                '2022',
                // value
                new \DateTime('2022-02-05')
            ],
            '1990' => [
                // expected
                '1990',
                // value
                new \DateTime('1990-10-10')
            ],
        ];
    }

    /**
     * @dataProvider getGetNbOfWorkingDaysBetweenDateTimesProvider
     */
    public function testGetNbOfWorkingDaysBetweenDateTimes(int $expected, \DateTime $start, \DateTime $end): void
    {
        $this->assertEquals($expected, DateUtils::getNbOfWorkingDaysBetweenDateTimes($start, $end));
    }

    public function getGetNbOfWorkingDaysBetweenDateTimesProvider(): array
    {
        return [
            'less than 24 hours' => [
                // expected
                1,
                // start
                new \DateTime('2022-07-18 08:00:00'),
                // end
                new \DateTime('2022-07-18 09:00:00'),
            ],
            'week same hour' => [
                // expected
                4,
                // start
                new \DateTime('2022-07-18'),
                // end
                new \DateTime('2022-07-22'),
            ],
            'week end after start' => [
                // expected
                5,
                // start
                new \DateTime('2022-07-18 00:00:00'),
                // end
                new \DateTime('2022-07-22 23:59:59'),
            ],
            'end_in_week_end' => [
                // expected
                1,
                // start
                new \DateTime('2022-07-22'),
                // end
                new \DateTime('2022-07-24'),
            ],
            'end_out_week_end' => [
                // expected
                7,
                // start
                new \DateTime('2022-07-20'),
                // end
                new \DateTime('2022-07-29'),
            ],
        ];
    }

    /**
     * @dataProvider getGetDateTimeFromMonthYearProvider
     */
    public function testGetDateTimeFromMonthYear(string $string): void
    {
        $this->assertEquals($string, DateUtils::getDateTimeFromMonthYear($string)->format('m/Y'));
    }

    public function getGetDateTimeFromMonthYearProvider(): array
    {
        return [
            'simple' => [
                '10/2022'
            ],
            'month_with_zero' => [
                '01/2021'
            ],
        ];
    }

    public function testGetDateTimeFromMonthYearMalformated(): void
    {
        $this->expectException(\RuntimeException::class);

        DateUtils::getDateTimeFromMonthYear('');
    }

    /**
     * @dataProvider getGetLastDayPreviousMonthFromDateTimeProvider
     */
    public function testGetLastDayPreviousMonthFromDateTime(string $expected, \DateTime $dateTime): void
    {
        $this->assertEquals($expected, DateUtils::getLastDayPreviousMonthFromDateTime($dateTime)->format('Y-m-d H:i:s'));
    }

    public function getGetLastDayPreviousMonthFromDateTimeProvider(): array
    {
        return [
            'case 30 days from first day' => [
                '2021-09-30 23:59:59',
                new \DateTime('2021-10-01')
            ],
            'case 31 days from first day' => [
                '2021-08-31 23:59:59',
                new \DateTime('2021-09-01')
            ],
            'case 30 days from last day' => [
                '2021-09-30 23:59:59',
                new \DateTime('2021-10-31')
            ],
            'case 31 days from last day' => [
                '2021-08-31 23:59:59',
                new \DateTime('2021-09-30')
            ],
            'case first day year' => [
                '2020-12-31 23:59:59',
                new \DateTime('2021-01-01')
            ],
            'case april first' => [
                '2021-03-31 23:59:59',
                new \DateTime('2021-04-01')
            ],
            'case july first' => [
                '2021-06-30 23:59:59',
                new \DateTime('2021-07-01')
            ],
        ];
    }

    /**
     * @dataProvider getGetFirstDayNextMonthFromDateTimeProvider
     */
    public function testGetFirstDayNextMonthFromDateTime(string $expected, \DateTime $dateTime): void
    {
        $this->assertEquals($expected, DateUtils::getFirstDayNextMonthFromDateTime($dateTime)->format('Y-m-d'));
    }

    public function getGetFirstDayNextMonthFromDateTimeProvider(): array
    {
        return [
            '01/01' => [
                '2022-02-01',
                new \DateTime('2022-01-01')
            ],
            '31/07' => [
                '2022-08-01',
                new \DateTime('2022-07-31')
            ]
        ];
    }

    /**
     * @dataProvider getGetFirstDayMonthProvider
     */
    public function testGetFirstDayMonth(string $expected, \DateTime $dateTime): void
    {
        $this->assertEquals($expected, DateUtils::getFirstDayMonth($dateTime)->format('d/m/Y H:i:s'));
    }

    public function getGetFirstDayMonthProvider(): array
    {
        return [
            'simple' => [
                '01/06/2021 00:00:00',
                new \DateTime('2021-06-06')
            ],
            'end_month' => [
                '01/08/2021 00:00:00',
                new \DateTime('2021-08-31')
            ],
        ];
    }

    /**
     * @dataProvider getNextBirthdayDateTimeProvider
     */
    public function testGetNextBirthdayDateTime(string $expected, \DateTime $birthday, \DateTime $currentDay): void
    {
        $this->assertSame($expected, DateUtils::getNextBirthdayDateTime($birthday, $currentDay)->format('Y-m-d'));
    }

    public function getNextBirthdayDateTimeProvider(): array
    {
        return [
            'case plus one year' => [
                '2022-10-27',
                new \DateTime('2000-10-27'),
                new \DateTime('2021-12-08')
            ],
            'case same year' => [
                '2021-12-01',
                new \DateTime('2018-12-01'),
                new \DateTime('2021-05-13')
            ],
            'case plus one year same day' => [
                '2022-10-27',
                (new \DateTime('2000-10-27'))->setTime(8, 0),
                (new \DateTime('2021-10-27'))->setTime(9, 0),
            ],
            'case same date' => [
                '2022-10-27',
                (new \DateTime('2021-10-27'))->setTime(8, 0),
                (new \DateTime('2021-10-27'))->setTime(8, 0),
            ],
        ];
    }

    /**
     * @dataProvider getGetFormattedLongMonthProvider
     */
    public function testGetFormattedLongMonth(string $expected, string $date, string $locale): void
    {
        $this->assertEquals($expected, DateUtils::getFormattedLongMonth(new \DateTime($date), $locale));
    }

    public function getGetFormattedLongMonthProvider(): array
    {
        return [
            'Janvier' => ['Janvier', '2022-01-15', 'fr_FR'],
            'Octobre' => ['Octobre', '2020-10-01', 'fr_FR'],
            'January' => ['January', '2020-01-01', 'en_EN'],
        ];
    }

    /**
     * @dataProvider getGetFormattedShortMonthProvider
     */
    public function testGetFormattedShortMonth(string $expected, string $date, string $locale): void
    {
        $this->assertEquals($expected, DateUtils::getFormattedShortMonth(new \DateTime($date), $locale));
    }

    public function getGetFormattedShortMonthProvider(): array
    {
        return [
            'Jan (fr)' => ['Jan', '2022-01-15', 'fr_FR'],
            'Juin' => ['Juin', '2020-06-01', 'fr_FR'],
            'Jan (en)' => ['Jan', '2020-01-01', 'en_EN'],
        ];
    }

    /**
     * @dataProvider getGetFormattedLongMonthYearsProvider
     */
    public function testGetFormattedLongMonthYears(string $expected, string $date, string $locale): void
    {
        $this->assertEquals($expected, DateUtils::getFormattedLongMonthYears(new \DateTime($date), $locale));
    }

    public function getGetFormattedLongMonthYearsProvider(): array
    {
        return [
            'Janvier 2022' => ['Janvier 2022', '2022-01-15', 'fr_FR'],
            'Octobre 2020' => ['Octobre 2020', '2020-10-01', 'fr_FR'],
            'January 2020' => ['January 2020', '2020-01-01', 'en_EN'],
        ];
    }

    /**
     * @dataProvider getFormattedShortMonthYearsProvider
     */
    public function testGetFormattedShortMonthYears(string $expected, string $date): void
    {
        $this->assertEquals($expected, DateUtils::getFormattedShortMonthYears(new \DateTime($date)));
    }

    public function getFormattedShortMonthYearsProvider(): array
    {
        return [
            'Janvier 2022' => ['Jan. 22', '2022-01-15'],
            'Octobre 2020' => ['Oct. 20', '2020-10-01'],
            'Juin 2020' => ['Juin. 20', '2020-06-01'],
            'Juillet 2020' => ['Jui. 20', '2020-07-01'],
        ];
    }

    /**
     * @dataProvider getGetNbDayBetweenDateTimesProvider
     */
    public function testGetNbDayBetweenDateTimes(int $expected, \DateTime $start, \DateTime $end): void
    {
        $this->assertEquals($expected, DateUtils::getNbDayBetweenDateTimes($start, $end));
    }

    public function getGetNbDayBetweenDateTimesProvider(): array
    {
        return [
            'lass than 24 hours' => [0, new \DateTime('2022-07-18 08:00:00'), new \DateTime('2022-07-18 15:00:00')],
            '3 day' => [3, new \DateTime('2022-07-18 23:59:59'), new \DateTime('2022-07-22 00:00:00')],
            '4 day same date 3 day with large hours' => [4, new \DateTime('2022-07-18 00:00:00'), new \DateTime('2022-07-22 23:59:59')],
            'long' => [139, new \DateTime('2022-01-01 10:00:00'), new \DateTime('2022-05-20 10:00:00')],
        ];
    }

    public function testAddDays(): void
    {
        $this->assertEquals(new \DateTime('2024-10-15 08:00:00'), DateUtils::addDays(new \DateTime('2024-10-10 08:00:00'), 5));
    }

    /**
     * @dataProvider addWorkingDaysProvider
     */
    public function testAddWorkingDays(string $dateTime, int $daysNb, string $expect): void
    {
        $this->assertSame(
            $expect,
            DateUtils::addWorkingDays(new \DateTime($dateTime), $daysNb)->format('Y-m-d')
        );
    }

    public function addWorkingDaysProvider(): array
    {
        return [
            '10_days' => ['2024-03-13', 10, '2024-03-27'],
            '20_days' => ['2024-03-01', 20, '2024-03-29'],
            'one_day_with_week_end' => ['2024-03-15', 1, '2024-03-18'],
            'calculate_on_bissextile_year' => ['2024-02-28', 6, '2024-03-07'],
        ];
    }

    public function testSubDays(): void
    {
        $this->assertEquals(new \DateTime('2024-10-05 08:00:00'), DateUtils::subDays(new \DateTime('2024-10-10 08:00:00'), 5));
    }

    public function testAddMonths(): void
    {
        $this->assertEquals(new \DateTime('2025-03-10 08:00:00'), DateUtils::addMonths(new \DateTime('2024-10-10 08:00:00'), 5));
    }

    public function testSubMonths(): void
    {
        $this->assertEquals(new \DateTime('2024-05-10 08:00:00'), DateUtils::subMonths(new \DateTime('2024-10-10 08:00:00'), 5));
    }

    public function testAddYears(): void
    {
        $this->assertEquals(new \DateTime('2029-10-10 08:00:00'), DateUtils::addYears(new \DateTime('2024-10-10 08:00:00'), 5));
    }

    public function testSubYears(): void
    {
        $this->assertEquals(new \DateTime('2019-10-10 08:00:00'), DateUtils::subYears(new \DateTime('2024-10-10 08:00:00'), 5));
    }

    /**
     * @dataProvider secondsToStringProvider
     */
    public function testSecondsToString(?string $expected, ?int $value): void
    {
        $this->assertEquals($expected, DateUtils::secondsToString($value));
    }

    public function secondsToStringProvider(): array
    {
        return [
            'null value' => [null, null],
            '0 seconds' => ['0s', 0],
            '10 seconds' => ['10s', 10],
            '1 minutes' => ['1m', 60],
            '1 minutes and 30 seconds' => ['1m 30s', 90],
            '1 hours' => ['1h', 3600],
            '1 hours 15 minutes' => ['1h 15m', 4500],
            '1 hours 1 minutes and 1 seconds' => ['1h 1m 1s', 3661],
            '3 hours' => ['3h', 10800],
        ];
    }

    /**
     * @dataProvider millisecondsToStringProvider
     */
    public function testMillisecondsToString(?string $expected, ?int $value): void
    {
        $this->assertEquals($expected, DateUtils::millisecondsToString($value));
    }

    public function millisecondsToStringProvider(): array
    {
        return [
            'null value' => [null, null],
            '0 milliseconds' => ['0ms', 0],
            '12 milliseconds' => ['12ms', 12],
            '123 milliseconds' => ['123ms', 123],
            '1000 milliseconds to 1 second' => ['1s', 1000],
            'exact seconds and no milliseconds' => ['7s', 7000],
            'seconds and milliseconds' => ['26s 457ms', 26457],
            '1 minute' => ['1m', 60000],
            '1 minute and some milliseconds' => ['1m', 60123],
            // all timing cases above falls into the testSecondsToString
        ];
    }
}
