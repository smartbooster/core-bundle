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
}
