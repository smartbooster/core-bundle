<?php

namespace Smart\CoreBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
interface ImageableInterface
{
    public const IMAGE_MAPPING = 'image';
    public const IMAGE_MAX_SIZE = '8M';
    public const IMAGE_MAX_WIDTH = '1000';
    public const IMAGE_MAX_HEIGHT = '1000';

    public function hasImage(): bool;

    public function getFormattedImageSize(): ?string;

    public function setImageFile(File|false|null $file = null): void;

    public function getImageFile(): File|false|null;

    public function getImageOriginalName(): ?string;

    public function setImageOriginalName(?string $name): self;

    public function getImageName(): ?string;

    public function setImageName(?string $name): self;

    public function getImageSize(): ?float;

    public function setImageSize(?float $size): self;
}
