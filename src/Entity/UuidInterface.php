<?php

namespace Smart\CoreBundle\Entity;

use Symfony\Component\Uid\Uuid;

interface UuidInterface
{
    public function getUuid(): ?Uuid; // @phpstan-ignore-line

    public function setUuid(?Uuid $uuid): static; // @phpstan-ignore-line
}
