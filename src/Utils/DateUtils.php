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
            $month = $formatter->format(strtotime("2000-$i")); // MDT the year is arbitrary and does not impact the month
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
     * get DateTime from month/year string
     *
     * <pre>
     * <?php
     * getDateTimeFromMonthYear('10/2022');
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * new DateTime('2022-10-01')
     * </pre>
     */
    public static function getDateTimeFromMonthYear(string $string): \DateTime
    {
        $endDateElements = explode('/', $string);

        if (count($endDateElements) <= 1) {
            throw new \RuntimeException('string must have mm/yyyy format');
        }

        $toReturn = new \DateTime();
        $toReturn->setDate((int) $endDateElements[1], (int) $endDateElements[0], 1);

        return $toReturn;
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

    public static function getFirstDayYearFromDateTime(?\DateTime $datetime = null): \DateTime
    {
        if ($datetime == null) {
            $datetime = new \DateTime();
        }

        return new \DateTime($datetime->format('Y') . '-01-01');
    }

    public static function getLastDayMonthFromDateTime(?\DateTime $datetime = null): \DateTime
    {
        if ($datetime == null) {
            $datetime = new \DateTime();
        }

        return new \DateTime($datetime->format('Y-m-t 23:59:59'));
    }

    /**
     * Get array of year month between 2 DateTime
     * It's work if end is before start
     *
     * <pre>
     * <?php
     * getMonthsBetweenDateTimes(new \DateTime('2015-05-14'), new \DateTime('2015-09-02'));
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * ['2015-05', '2015-06', '2015-07', '2015-08', '2015-09']
     * </pre>
     */
    public static function getMonthsBetweenDateTimes(\DateTime $start, \DateTime $end): array
    {
        if ($start > $end) {
            return self::getMonthsBetweenDateTimes($end, $start);
        }

        $startDate = clone $start;
        $endDate = clone $end;

        $startDate = $startDate->modify('first day of this month');
        $endDate = $endDate->modify('first day of next month');
        // set to 00:00:00 to be sure the period don't include additional month on hour end > hour start
        // start_after_end example on DateUtilsTest
        $endDate->setTime(0, 0);
        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($startDate, $interval, $endDate);

        $months = [];
        foreach ($period as $datetime) {
            $months[] = $datetime->format('Y-m');
        }

        return $months;
    }

    public static function getDateTimeMonth(\DateTime $dateTime): string
    {
        return $dateTime->format('m');
    }

    public static function getDateTimeYear(\DateTime $dateTime): string
    {
        return $dateTime->format('Y');
    }

    /**
     * Get number of Working days between DateTime
     * The duration is calculated by 24-hour period. the time can influence the result. Every 24 hour started is counted
     * Inspiration : https://stackoverflow.com/questions/336127/calculate-business-days
     *
     * <pre>
     * <?php
     * getNbOfWorkingDaysBetweenDateTimes(new \DateTime('2022-07-18 23:59:59'), new \DateTime('2022-07-22 00:00:00'));
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * 4
     * </pre>
     * <pre>
     * <?php
     * getNbOfWorkingDaysBetweenDateTimes(new \DateTime('2022-07-18 00:00:00'), new \DateTime('2022-07-22 23:59:59'));
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * 5
     * </pre>
     */
    public static function getNbOfWorkingDaysBetweenDateTimes(\DateTime $start, \DateTime $end): int
    {
        $workingDays = [1, 2, 3, 4, 5];

        $end = clone $end;
        $interval = new \DateInterval('P1D');
        $periods = new \DatePeriod($start, $interval, $end);

        $days = 0;
        foreach ($periods as $period) {
            if (!in_array($period->format('N'), $workingDays)) {
                continue;
            }
            $days++;
        }

        return $days;
    }

    /**
     * Get number of days between DateTime
     * The duration is calculated by 24-hour period. the time can influence the result. Every 24 hour started is counted. The first 24h not count
     *
     * <pre>
     * <?php
     * getNbDayBetweenDateTimes(new \DateTime('2022-07-18 23:59:59'), new \DateTime('2022-07-22 00:00:00'));
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * 3
     * </pre>
     * <pre>
     * <?php
     * getNbDayBetweenDateTimes(new \DateTime('2022-07-18 00:00:00'), new \DateTime('2022-07-22 23:59:59'));
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * 4
     * </pre>
     */
    public static function getNbDayBetweenDateTimes(?\DateTimeInterface $start, ?\DateTimeInterface $end): ?int
    {
        if (!$start instanceof \DateTimeInterface || !$end instanceof \DateTimeInterface) {
            return null;
        }

        return (int) date_diff($start, $end)->days;
    }

    public static function isNighttime(string $timezone = 'Europe/Paris'): bool
    {
        $dateTime = new \DateTime($timezone);
        $currentHour = intval($dateTime->format('H'));

        return $currentHour >= 18;
    }

    /**
     * Return last time of last day of last month of datetime
     */
    public static function getLastDayPreviousMonthFromDateTime(\DateTime $datetime): \DateTime
    {
        $clonedDateTime = clone $datetime;
        return $clonedDateTime->modify('last day of previous month')->setTime(23, 59, 59);
    }

    public static function getFirstDayNextMonthFromDateTime(\DateTime $datetime): \DateTime
    {
        $clonedDateTime = clone $datetime;
        return $clonedDateTime->modify('first day of next month');
    }

    /**
     * Return first time of first day of month of datetime
     */
    public static function getFirstDayMonth(\DateTime $datetime): \DateTime
    {
        $clonedDateTime = clone $datetime;
        return $clonedDateTime->modify('first day of this month')->setTime(0, 0);
    }

    /**
     * Return next birthday of a DateTime
     * If birthday > currentDay, it returns birthday
     *
     * @param \DateTime|null $currentDay if null it's use current date
     */
    public static function getNextBirthdayDateTime(\DateTimeInterface $birthday, ?\DateTime $currentDay = null): \DateTimeInterface
    {
        /** @var \DateTime $nextBirthday */
        $nextBirthday = clone $birthday;
        if (!$currentDay instanceof \DateTime) {
            $currentDay = new \DateTime();
        }
        $nextBirthday->setDate((int)$currentDay->format('Y'), (int)$birthday->format('m'), (int)$birthday->format('d'));
        if ($nextBirthday <= $currentDay) {
            $nextBirthday->modify('next year');
        }

        return $nextBirthday;
    }

    /**
     * Return full month in locale format
     *
     * <pre>
     * <?php
     * getFormattedLongMonth(new \DateTime('2022-01-15'));
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * Janvier
     * </pre>
     */
    public static function getFormattedLongMonth(\DateTime $date, string $locale = 'fr_FR'): string
    {
        $dateFormatter = new \IntlDateFormatter($locale, \IntlDateFormatter::NONE, \IntlDateFormatter::NONE);
        $dateFormatter->setPattern('MMMM');

        return ucfirst((string) $dateFormatter->format($date));
    }

    /**
     * Return short month locale format, substring 3 char except "Juin"
     *
     * <pre>
     * <?php
     * getFormattedShortMonth(new \DateTime('2022-01-15'));
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * Jan
     * </pre>
     */
    public static function getFormattedShortMonth(\DateTime $date, string $locale = 'fr_FR'): string
    {
        $month = self::getFormattedLongMonth($date, $locale);

        // It must be "Juin" in full otherwise there be multiple "Jui." key with "Juillet"
        if ($month === 'Juin') {
            return $month;
        }

        return mb_substr((string)$month, 0, 3);
    }

    /**
     * Return full month year locale format
     *
     * <pre>
     * <?php
     * getFormattedLongMonth(new \DateTime('2022-01-15'));
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * Janvier 2022
     * </pre>
     */
    public static function getFormattedLongMonthYears(\DateTime $date, string $locale = 'fr_FR'): string
    {
        $dateFormatter = new \IntlDateFormatter($locale, \IntlDateFormatter::NONE, \IntlDateFormatter::NONE);
        $dateFormatter->setPattern('MMMM yyyy');

        return ucfirst((string) $dateFormatter->format($date));
    }

    /**
     * Return short month year french format
     *
     * <pre>
     * <?php
     * getFormattedLongMonth(new \DateTime('2022-01-15'));
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * Jan. 2022
     * </pre>
     */
    public static function getFormattedShortMonthYears(\DateTime $date): string
    {
        $monthDateFormatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::NONE, \IntlDateFormatter::NONE);
        $yearDateFormatter = clone $monthDateFormatter;
        // pattern : https://unicode-org.github.io/icu/userguide/format_parse/datetime/
        $monthDateFormatter->setPattern('MMMM');
        $month = $monthDateFormatter->format($date);
        $yearDateFormatter->setPattern('yy');
        $year = $yearDateFormatter->format($date);

        // It must be "juin" in full otherwise there be multiple "jui." key with "juillet"
        if ($month === 'juin') {
            $mbSubMonth = $month;
        } else {
            $mbSubMonth = mb_substr((string) $month, 0, 3);
        }

        return ucfirst($mbSubMonth) . '. ' . $year;
    }

    public static function addDays(\DateTime $dateTime, int $daysNb): \DateTime
    {
        $clonedDateTime = clone $dateTime;
        $clonedDateTime->add(new \DateInterval("P{$daysNb}D"));

        return $clonedDateTime;
    }

    public static function addWorkingDays(\DateTime $dateTime, int $daysNb): \DateTime
    {
        $clonedDateTime = clone $dateTime;
        $clonedDateTime->modify("+$daysNb Weekday");

        return $clonedDateTime;
    }

    public static function subDays(\DateTime $dateTime, int $daysNb): \DateTime
    {
        $clonedDateTime = clone $dateTime;
        $clonedDateTime->sub(new \DateInterval("P{$daysNb}D"));

        return $clonedDateTime;
    }

    public static function addMonths(\DateTime $dateTime, int $monthsNb): \DateTime
    {
        $clonedDateTime = clone $dateTime;
        $clonedDateTime->add(new \DateInterval("P{$monthsNb}M"));

        return $clonedDateTime;
    }

    public static function subMonths(\DateTime $dateTime, int $monthsNb): \DateTime
    {
        $clonedDateTime = clone $dateTime;
        $clonedDateTime->sub(new \DateInterval("P{$monthsNb}M"));

        return $clonedDateTime;
    }

    public static function addYears(\DateTime $dateTime, int $yearsNb): \DateTime
    {
        $clonedDateTime = clone $dateTime;
        $clonedDateTime->add(new \DateInterval("P{$yearsNb}Y"));

        return $clonedDateTime;
    }

    public static function subYears(\DateTime $dateTime, int $yearsNb): \DateTime
    {
        $clonedDateTime = clone $dateTime;
        $clonedDateTime->sub(new \DateInterval("P{$yearsNb}Y"));

        return $clonedDateTime;
    }

    public static function secondsToString(?int $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $toReturn = '';
        $hours = floor($value / 3600);
        $remainingSeconds = $value % 3600;
        $minutes = floor($remainingSeconds / 60);
        $seconds = $remainingSeconds % 60;
        if ($hours > 0) {
            $toReturn .= "{$hours}h ";
        }
        if ($minutes > 0) {
            $toReturn .= "{$minutes}m ";
        }
        if ($toReturn === '' || $seconds > 0) {
            $toReturn .= "{$seconds}s";
        }

        return trim($toReturn);
    }

    public static function millisecondsToString(?int $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $toReturn = self::secondsToString((int) ($value / 1000)); // We reduce it to the seconds to benefit from secondsToString
        if ($value > 60000) { // We act that printing the number of ms is only relevant if the $value is under a minute.
            return $toReturn;
        } elseif ($value < 1000) { // If inferior to 1 second we remove the '0s'
            $toReturn = '';
        }

        $milliseconds = $value % 1000;
        if ($toReturn === '' || $milliseconds !== 0) {
            $toReturn .= " {$milliseconds}ms";
        }

        return trim($toReturn);
    }
}
