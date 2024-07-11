<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Smart\CoreBundle\Utils\MathUtils;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 *
 * @ORM\Embeddable()
 */
#[ORM\Embeddable]
class EmbeddedFile
{
    /**
     * File name based on the originalName slug concatenated with a hash
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?string $name = null;

    /**
     * Original name of the uploaded file
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?string $originalName = null;

    /**
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?float $size = null;

    /**
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null; // @phpstan-ignore-line

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): void
    {
        $this->originalName = $originalName;
    }

    public function getSize(): ?float
    {
        return $this->size;
    }

    public function setSize(?float $size): void
    {
        $this->size = $size;
    }

    public function getFormattedSize(): ?string
    {
        $size = $this->getSize();
        if ($size === null) {
            return null;
        }

        return MathUtils::formatBytes($size);
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
