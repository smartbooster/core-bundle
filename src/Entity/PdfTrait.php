<?php

namespace Smart\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Smart\CoreBundle\Utils\MathUtils;
use Smart\CoreBundle\Utils\MimeTypesUtils;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

trait PdfTrait
{
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property to use in form type.
     * If the project use annotation then the entity using this trait must add the following :
     *  @Vich\UploadableField(mapping="%mapping_name%", fileNameProperty="pdfName", size="pdfSize", originalName="pdfOriginalName")
     *  @Assert\File(maxSize=self::PDF_MAX_SIZE, mimeTypes=MimeTypesUtils::PDF)
     *  protected File|false|null $pdfFile = null;
     */
    #[Vich\UploadableField(mapping: self::PDF_MAPPING, fileNameProperty: "pdfName", size:"pdfSize", originalName: "pdfOriginalName")]
    #[Assert\File(maxSize: self::PDF_MAX_SIZE, mimeTypes: MimeTypesUtils::PDF)]
    protected File|false|null $pdfFile = null;

    /**
     * Original name of the uploaded pdf
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?string $pdfOriginalName = null;

    /**
     * Pdf name based on the pdfOriginalName slug concatenated with a hash
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?string $pdfName = null;

    /**
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?float $pdfSize = null;

    /**
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    protected ?\DateTimeImmutable $pdfUpdatedAt = null;

    public function hasPdf(): bool
    {
        return $this->pdfName !== null;
    }

    public function getFormattedPdfSize(): ?string
    {
        $size = $this->getPdfSize();
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
    public function setPdfFile(File|false|null $file = null): void
    {
        $this->pdfFile = $file;

        if ($file instanceof UploadedFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->pdfUpdatedAt = new \DateTimeImmutable();
            if ($this instanceof UuidInterface && $this->getUuid() === null) {
                $this->setUuid(Uuid::v7());
            }
        }
    }

    public function getPdfFile(): File|false|null
    {
        return $this->pdfFile;
    }

    public function getPdfOriginalName(): ?string
    {
        return $this->pdfOriginalName;
    }

    public function setPdfOriginalName(?string $name): self
    {
        $this->pdfOriginalName = $name;

        return $this;
    }

    public function getPdfName(): ?string
    {
        return $this->pdfName;
    }

    public function setPdfName(?string $name): self
    {
        $this->pdfName = $name;

        return $this;
    }

    public function getPdfSize(): ?float
    {
        return $this->pdfSize;
    }

    public function setPdfSize(?float $size): self
    {
        $this->pdfSize = $size;

        return $this;
    }
}
