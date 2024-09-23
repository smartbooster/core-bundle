<?php

namespace Smart\CoreBundle\Entity;

/**
 * The archive entity must have the __toString method because his string representation is shown on flash message
 *
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
interface ArchivableInterface extends \Stringable
{
    public function getArchivedAt(): ?\DateTimeInterface;

    public function setArchivedAt(?\DateTimeInterface $archivedAt): self;

    public function isArchived(): bool;
}
