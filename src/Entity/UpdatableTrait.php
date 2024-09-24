<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait UpdatableTrait
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $updatedAt = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?int $updatedAtMonth = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?int $updatedAtYear = null;

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt, bool $initIntegerFields = true): void
    {
        $this->updatedAt = $updatedAt;
        if ($updatedAt !== null && $initIntegerFields) {
            $this->updatedAtMonth = (int) $updatedAt->format('m');
            $this->updatedAtYear = (int) $updatedAt->format('Y');
        }
    }

    public function getUpdatedAtMonth(): ?int
    {
        return $this->updatedAtMonth;
    }

    public function setUpdatedAtMonth(?int $updatedAtMonth): void
    {
        $this->updatedAtMonth = $updatedAtMonth;
    }

    public function getUpdatedAtYear(): ?int
    {
        return $this->updatedAtYear;
    }

    public function setUpdatedAtYear(?int $updatedAtYear): void
    {
        $this->updatedAtYear = $updatedAtYear;
    }
}
