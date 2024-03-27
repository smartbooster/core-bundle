<?php

namespace Smart\CoreBundle\Entity;

interface PersonNameableInterface
{
    public function getFullName(): ?string;

    public function getInitial(): string;

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName): self;

    public function getLastName(): ?string;

    public function setLastName(?string $lastName): self;
}
