<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Smart\CoreBundle\Utils\RegexUtils;

trait PhoneTrait
{
    /**
     * @ORM\Column(length=20, nullable=true)
     * @Assert\Length(max=20)
     * @Assert\Regex(pattern=RegexUtils::PHONE_PATTERN, message=RegexUtils::PHONE_MESSAGE)
     */
    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    #[Assert\Regex(pattern: RegexUtils::PHONE_PATTERN, message: RegexUtils::PHONE_MESSAGE)]
    private ?string $phone = null;

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }
}
