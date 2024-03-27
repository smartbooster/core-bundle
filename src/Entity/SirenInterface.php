<?php

namespace Smart\CoreBundle\Entity;

interface SirenInterface
{
    public function getSiren(): ?string;

    public function setSiren(?string $siren): static;
}
