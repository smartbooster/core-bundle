<?php

namespace Smart\CoreBundle\Entity;

interface EmailInterface
{
    public function getEmail(): ?string;

    public function setEmail(?string $email): self;
}
