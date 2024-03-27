<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait WebsiteTrait
{
    #[ORM\Column(length: 300, nullable: true)]
    #[Assert\Length(max: 300)]
    #[Assert\Url]
    private ?string $website = null;

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }
}
