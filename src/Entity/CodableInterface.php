<?php

namespace Smart\CoreBundle\Entity;

interface CodableInterface
{
    public function getCode(): ?string;

    public function setCode(?string $code): self;

    public function hasCode(): bool;
}
