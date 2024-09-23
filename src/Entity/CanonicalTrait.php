<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
trait CanonicalTrait
{
    /**
     * @ORM\Column(type="string", length=500, unique=true, nullable=true)
     * @Assert\Length(max=500)
     */
    #[ORM\Column(length: 500, unique: true, nullable: true)]
    #[Assert\Length(max: 500)]
    protected ?string $canonical = null;

    public function getCanonical(): ?string
    {
        return $this->canonical;
    }

    public function setCanonical(?string $canonical): static
    {
        $this->canonical = $canonical;

        return $this;
    }
}
