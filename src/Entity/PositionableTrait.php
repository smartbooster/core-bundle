<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
trait PositionableTrait
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?int $position = null;

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
