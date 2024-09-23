<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait SearchableTrait
{
    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    #[ORM\Column(length: 500, nullable: true)]
    protected ?string $search = null;

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): self
    {
        $this->search = substr(strtolower($search), 0, 500);

        return $this;
    }
}
