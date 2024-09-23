<?php

namespace Smart\CoreBundle\Entity;

interface EnableInterface
{
    public function isEnabled(): ?bool;

    public function setEnabled(bool $enabled): self;
}
