<?php

namespace Smart\CoreBundle\Entity;

interface UpdatableInterface
{
    public function getUpdatedAt(): \DateTimeInterface;

    public function setUpdatedAt(\DateTimeInterface $updatedAt, bool $initIntegerFields = true): void;

    public function getUpdatedAtMonth(): ?int;

    public function setUpdatedAtMonth(?int $updatedAtMonth): void;

    public function getUpdatedAtYear(): ?int;

    public function setUpdatedAtYear(?int $updatedAtYear): void;
}
