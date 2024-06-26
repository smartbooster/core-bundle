<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Smart\CoreBundle\Utils\MathUtils;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

trait ImageableTrait
{
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property to use in form type.
     * If the project use annotation then the entity using this trait must add the following :
     *  @Vich\UploadableField(mapping="%mapping_name%", fileNameProperty="imageName", size="imageSize", originalName="imageOriginalName")
     *  @Assert\Image(maxSize=self::IMAGE_MAX_SIZE, maxWidth=self::IMAGE_MAX_WIDTH, maxHeight=self::IMAGE_MAX_HEIGHT)
     *  protected File|false|null $imageFile = null;
     */
    #[Vich\UploadableField(mapping: self::IMAGE_MAPPING, fileNameProperty: "imageName", size:"imageSize", originalName: "imageOriginalName")]
    #[Assert\Image(maxSize: self::IMAGE_MAX_SIZE, maxWidth: self::IMAGE_MAX_WIDTH, maxHeight: self::IMAGE_MAX_HEIGHT)]
    protected File|false|null $imageFile = null;

    /**
     * Original name of the uploaded image
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?string $imageOriginalName = null;

    /**
     * Image name based on the imageOriginalName slug concatenated with a hash
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?string $imageName = null;

    /**
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?float $imageSize = null;

    /**
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?\DateTimeImmutable $imageUpdatedAt = null;

    public function hasImage(): bool
    {
        return $this->imageName !== null;
    }

    public function getFormattedImageSize(): ?string
    {
        $size = $this->getImageSize();
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
    public function setImageFile(File|false|null $file = null): void
    {
        $this->imageFile = $file;

        if ($file instanceof UploadedFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->imageUpdatedAt = new \DateTimeImmutable();
            if ($this instanceof UuidInterface && $this->getUuid() === null) {
                $this->setUuid(Uuid::v7());
            }
        }
    }

    public function getImageFile(): File|false|null
    {
        return $this->imageFile;
    }

    public function getImageOriginalName(): ?string
    {
        return $this->imageOriginalName;
    }

    public function setImageOriginalName(?string $name): self
    {
        $this->imageOriginalName = $name;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $name): self
    {
        $this->imageName = $name;

        return $this;
    }

    public function getImageSize(): ?float
    {
        return $this->imageSize;
    }

    public function setImageSize(?float $size): self
    {
        $this->imageSize = $size;

        return $this;
    }
}
