<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait PersonNameableTrait
{
    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    protected ?string $firstName = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    protected ?string $lastName = null;

    public function getFullName(): ?string
    {
        $toReturn = trim(sprintf('%s %s', $this->getFirstName(), $this->getLastName()));
        if ($toReturn === '') {
            return null;
        }

        return $toReturn;
    }

    public function getInitial(): string
    {
        return sprintf(
            '%s%s',
            substr(trim($this->getFirstName()), 0, 1),
            substr(trim($this->getLastName()), 0, 1)
        );
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }
}
