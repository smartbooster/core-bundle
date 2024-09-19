<?php

namespace Smart\CoreBundle\Entity;

interface CreatableInterface
{
    public function getCreatedAt(): ?\DateTime;

    public function setCreatedAt(?\DateTime $createdAt): static;
}
