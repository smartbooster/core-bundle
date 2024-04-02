<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Smart\CoreBundle\Utils\RegexUtils;

trait SirenTrait
{
    /**
     * @ORM\Column(length=20, nullable=true)
     * @Assert\Length(max=20)
     * @Assert\Regex(pattern=RegexUtils::SIREN_PATTERN, message=RegexUtils::SIREN_MESSAGE)
     */
    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    #[Assert\Regex(pattern: RegexUtils::SIREN_PATTERN, message: RegexUtils::SIREN_MESSAGE)]
    private ?string $siren = null;

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(?string $siren): static
    {
        $this->siren = $siren;

        return $this;
    }
}
