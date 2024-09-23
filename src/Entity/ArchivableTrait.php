<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait ArchivableTrait
{
    /**
     * @ORM\Column(name="archived_at", type="datetime", nullable=true)
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected \DateTime|\DateTimeInterface|null $archivedAt = null;

    public function getArchivedAt(): ?\DateTimeInterface
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?\DateTimeInterface $archivedAt): self
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->getArchivedAt() != null;
    }
}
