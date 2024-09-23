<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait EnableTrait
{
    /**
     * @ORM\Column(type="boolean", options={"default"=1})
     */
    #[ORM\Column(options: ["default" => 1])]
    protected ?bool $enabled = true;

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
