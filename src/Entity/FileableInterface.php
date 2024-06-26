<?php

namespace Smart\CoreBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;

interface FileableInterface
{
    public function hasFile(): bool;

    public function getFormattedFileSize(): ?string;

    public function setFileFile(File|false|null $file = null): void;

    public function getFileFile(): File|false|null;

    public function getFileOriginalName(): ?string;

    public function setFileOriginalName(?string $name): self;

    public function getFileName(): ?string;

    public function setFileName(?string $name): self;

    public function getFileSize(): ?float;

    public function setFileSize(?float $size): self;
}
