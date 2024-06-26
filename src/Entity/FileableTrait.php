<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Smart\CoreBundle\Utils\MathUtils;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait FileableTrait
{
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property to use in form type.
     * If the project use annotation then the entity using this trait must add the following :
     *  @Vich\UploadableField(mapping="%mapping_name%", fileNameProperty="fileName", size="fileSize", originalName="fileOriginalName")
     *  protected File|false|null $fileFile = null;
     */
    #[Vich\UploadableField(mapping: self::FILE_MAPPING, fileNameProperty: "fileName", size:"fileSize", originalName: "fileOriginalName")]
    protected File|false|null $fileFile = null;

    /**
     * Original name of the uploaded file
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?string $fileOriginalName = null;

    /**
     * File name based on the fileOriginalName slug concatenated with a hash
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?string $fileName = null;

    /**
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?float $fileSize = null;

    /**
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?\DateTimeImmutable $fileUpdatedAt = null;

    public function hasFile(): bool
    {
        return $this->fileName !== null;
    }

    public function getFormattedFileSize(): ?string
    {
        $size = $this->getFileSize();
        if ($size === null) {
            return null;
        }

        return MathUtils::formatBytes($size);
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     */
    public function setFileFile(File|false|null $file = null): void
    {
        $this->fileFile = $file;

        if ($file instanceof UploadedFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->fileUpdatedAt = new \DateTimeImmutable();
            if ($this instanceof UuidInterface && $this->getUuid() === null) {
                $this->setUuid(Uuid::v7());
            }
        }
    }

    public function getFileFile(): File|false|null
    {
        return $this->fileFile;
    }

    public function getFileOriginalName(): ?string
    {
        return $this->fileOriginalName;
    }

    public function setFileOriginalName(?string $name): self
    {
        $this->fileOriginalName = $name;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $name): self
    {
        $this->fileName = $name;

        return $this;
    }

    public function getFileSize(): ?float
    {
        return $this->fileSize;
    }

    public function setFileSize(?float $size): self
    {
        $this->fileSize = $size;

        return $this;
    }
}
