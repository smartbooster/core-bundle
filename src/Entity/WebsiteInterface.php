<?php

namespace Smart\CoreBundle\Entity;

interface WebsiteInterface
{
    public function getWebsite(): ?string;

    public function setWebsite(?string $website): static;
}
