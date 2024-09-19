<?php

namespace Smart\CoreBundle\Entity;

interface NameableInterface
{
    public function getName(): ?string;

    public function setName(?string $name): self;
}
