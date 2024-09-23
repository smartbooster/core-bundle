<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait CodableTrait
{
    /**
     * If the code needs to be mandatory then specify it via an #[Assert\Callback] in the entity
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    #[ORM\Column(unique: true, nullable: true)]
    protected ?string $code = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function hasCode(): bool
    {
        return $this->getCode() != null;
    }
}
