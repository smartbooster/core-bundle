<?php

namespace Smart\CoreBundle\Entity;

interface MonthYearInterface
{
    public function getMonth(): int;

    public function setMonth(int $month): void;

    public function getYear(): int;

    public function setYear(int $year): void;

    public function setMonthYearFromDate(\DateTime $date): void;

    public function getFormattedMonth(): string;
}
