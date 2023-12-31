<?php

namespace Smart\CoreBundle\Utils;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class DateUtils
{
    public static function getMonthsBetween(\DateTimeInterface $startedAt, \DateTimeInterface $endedAt): array
    {
        $startedYear = (int) $startedAt->format('Y');
        $startedMonth = (int) $startedAt->format('m');
        $endedYear = (int) $endedAt->format('Y');
        $endedMonth = (int) $endedAt->format('m');
        $toReturn = [];

        for ($year = $startedYear; $year <= $endedYear; $year++) {
            $fromMonth = $startedMonth;
            if ($year !== $startedYear) {
                $fromMonth = 1;
            }
            for ($month = $fromMonth; $month <= 12; $month++) {
                if ($year === $endedYear && $month > $endedMonth) {
                    break;
                }
                $toReturn[] = sprintf("%s-%s", $year, self::getFormattedDayOrMonth($month));
            }
        }

        return $toReturn;
    }

    /**
     * Returns the number of the day or month formatted with the missing 0 if necessary
     */
    public static function getFormattedDayOrMonth(int $number): string
    {
        return $number < 10 ? "0$number" : (string) $number;
    }

    public static function getMonthChoices(string $locale = 'fr_FR'): array
    {
        $formatter = new \IntlDateFormatter(locale: $locale, dateType: \IntlDateFormatter::FULL, timeType: \IntlDateFormatter::FULL, pattern: 'MMMM');
        for ($i = 1; $i <= 12; $i++) {
            $month = $formatter->format(strtotime("2000-$i")); // @phpstan-ignore-line MDT the year is arbitrary and does not impact the month
            $toReturn[ucfirst((string) $month)] = $i;
        }

        return $toReturn;
    }

    public static function monthYearToString(?int $month = null, ?int $year = null, string $locale = 'fr_FR'): ?string
    {
        $formatter = new \IntlDateFormatter(locale: $locale, dateType: \IntlDateFormatter::FULL, timeType: \IntlDateFormatter::FULL, pattern: 'MMMM');
        if ($month !== null && $year !== null) {
            $month = $formatter->format(strtotime("$year-$month")); // @phpstan-ignore-line
            return ucfirst((string) $month) . ' ' . $year;
        }

        return null;
    }

    /**
     * @param array $options structure : [
     *      'start_hour' => int // Allows you to set a start time
     *      'end_hour' => int // Allows you to set an end time
     *  ]
     */
    public static function getDaysBetween(\DateTimeInterface $startedAt, \DateTimeInterface $endedAt, array $options = []): array
    {
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($startedAt, $interval, (clone $endedAt)->setTime(23, 59, 59)); // @phpstan-ignore-line
        $dateFormat = 'Y-m-d';

        $toReturn = [];
        foreach ($period as $date) {
            if (isset($options['start_hour']) && isset($options['end_hour'])) {
                $startDateHour = (int) $startedAt->format('H');
                $isBeforeStartHour = $startDateHour < $options['start_hour'] || (
                    $startDateHour === $options['start_hour'] && ((int) $startedAt->format('i')) === 0
                );
                $isAfterEndHour = ((int) $endedAt->format('H')) >= $options['end_hour'];

                if (
                    ($startedAt->format($dateFormat) === $endedAt->format($dateFormat) && (!$isBeforeStartHour || !$isAfterEndHour)) ||
                    ($date->format($dateFormat) === $startedAt->format($dateFormat) && !$isBeforeStartHour) ||
                    ($date->format($dateFormat) === $endedAt->format($dateFormat) && !$isAfterEndHour)
                ) {
                    continue;
                }
            }

            $toReturn[] = $date->format($options['format'] ?? $dateFormat);
        }

        return $toReturn;
    }

    /**
     * Returns all days of a month to be display in a calendar view
     */
    public static function getCalendarDays(int $month, int $year): array
    {
        $today = new \DateTime();
        $fistDayOfMonth = (new \DateTime())->setDate($year, $month, 1);
        $firstDayOfCalendar = clone $fistDayOfMonth;
        if ($fistDayOfMonth->format('N') !== '1') {
            $firstDayOfCalendar->modify('last Monday');
        }

        $lastDayOfMonth = clone (new \DateTime())->setDate($year, $month, (int)$fistDayOfMonth->format('t'));
        $lastDayOfCalendar = clone $lastDayOfMonth;
        // we will only search for the next Sunday if the day number is different from Sunday (7)
        if ($lastDayOfMonth->format('N') !== '7') {
            $lastDayOfCalendar->modify('next Sunday');
        }

        $toReturn = [];
        $currentDay = $firstDayOfCalendar;
        while ($currentDay->format('Y-m-d') !== $lastDayOfCalendar->format('Y-m-d')) {
            $isToday = $currentDay->format('Y-m-d') === $today->format('Y-m-d');
            $toReturn[$currentDay->format('Y-m-d')] = [
                'day' => (int) $currentDay->format('d'),
                'month' => (int) $currentDay->format('m'),
                'year' => (int) $currentDay->format('Y'),
                'isSameMonth' => (int) $currentDay->format('n') === $month,
                'isSameDay' => $today->format('d') === $currentDay->format('d') && (int) $currentDay->format('n') === $month,
                'isToday' => $isToday,
                'isWeekend' => (int) $currentDay->format('N') === 6 || (int)$currentDay->format('N') === 7,
                'isBeforeToday' => !$isToday && $currentDay < $today,
            ];

            $currentDay->modify('+1 day');
        };

        return $toReturn;
    }

    /**
     * Returns a date number format on the previous month
     *  - to obtain the previous month enter $format="n"
     *  - to obtain the year of the previous month, enter $format="Y"
     */
    public static function getPrevIntFormatFromMonthYear(int $month, int $year, string $format): int
    {
        $prevTime = (int) strtotime("$year-$month-01 -1 month");

        return (int) date($format, $prevTime);
    }

    /**
     * Returns a date number format on the next month
     *  - to obtain the next month enter $format="n"
     *  - to obtain the year of the next month, enter $format="Y"
     */
    public static function getNextIntFormatFromMonthYear(int $month, int $year, string $format): int
    {
        $prevTime = (int) strtotime("$year-$month-01 +1 month");

        return (int) date($format, $prevTime);
    }

    /**
     * Returns an array with all the values between 2 times according to a given interval
     */
    public static function getTimeIntervals(string $startTime, string $endTime, array $options = []): array
    {
        $toReturn = [];
        $currentTime = strtotime($startTime);
        $endTime = strtotime($endTime);
        if (!$currentTime || !$endTime) {
            return $toReturn;
        }

        $intervalMinutes = $options['interval_minutes'] ?? 15;
        while ($currentTime <= $endTime) {
            $toReturn[] = date("H:i", $currentTime);
            $currentTime += $intervalMinutes * 60; // Convert minutes to seconds
        }
        if ($options['exclude_time_params'] ?? false) {
            array_shift($toReturn);
            array_pop($toReturn);
        }
        if ($options['shift_start_time'] ?? false) {
            array_shift($toReturn);
        }
        if ($options['pop_end_time'] ?? false) {
            array_pop($toReturn);
        }

        return $toReturn;
    }

    public static function isTimeBeforeTime(string $time, string $toCompareTime): bool
    {
        return strtotime($time) < strtotime($toCompareTime);
    }

    public static function isTimeAfterTime(string $time, string $toCompareTime): bool
    {
        return strtotime($time) > strtotime($toCompareTime);
    }

    public static function getFirstDayOfMonthYear(int $month, int $year): \DateTime
    {
        return (new \DateTime())->setDate($year, $month, 1)->setTime(0, 0);
    }

    public static function getLastDayOfMonthYear(int $month, int $year): \DateTime
    {
        return self::getFirstDayOfMonthYear($month, $year)->modify('last day of this month')->setTime(23, 59, 59);
    }
}
