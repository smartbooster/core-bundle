<?php

namespace Smart\CoreBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
interface PdfInterface
{
    public const PDF_MAPPING = 'pdf';
    public const PDF_MAX_SIZE = '20M';

    public function hasPdf(): bool;

    public function getFormattedPdfSize(): ?string;

    public function setPdfFile(File|false|null $file = null): void;

    public function getPdfFile(): File|false|null;

    public function getPdfOriginalName(): ?string;

    public function setPdfOriginalName(?string $name): self;

    public function getPdfName(): ?string;

    public function setPdfName(?string $name): self;

    public function getPdfSize(): ?float;

    public function setPdfSize(?float $size): self;
}
