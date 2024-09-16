<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Smart\CoreBundle\Utils\DateUtils;
use Symfony\Component\Validator\Constraints as Assert;

trait MonthelableTrait
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(
     *      min = 1,
     *      max = 12,
     * )
     */
    #[Assert\Range(min: 1, max: 12)]
    #[ORM\Column(nullable: true)]
    protected ?int $month = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?int $year = null;

    public function getMonth(): int
    {
        return $this->month;
    }

    public function setMonth(int $month): void
    {
        $this->month = $month;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    public function setMonthelableDate(\DateTime $date): void
    {
        $this->month = (int) $date->format('n');
        $this->year = (int) $date->format('Y');
    }

    /**
     * Return formatted month ex: 01
     */
    public function getFormattedMonth(): string
    {
        return DateUtils::getFormattedDayOrMonth($this->month);
    }
}
