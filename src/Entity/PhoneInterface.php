<?php

namespace Smart\CoreBundle\Entity;

interface PhoneInterface
{
    public function getPhone(): ?string;

    public function setPhone(?string $phone): static;
}
