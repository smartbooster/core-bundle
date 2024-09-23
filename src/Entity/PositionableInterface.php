<?php

namespace Smart\CoreBundle\Entity;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
interface PositionableInterface
{
    public function getPosition(): ?int;

    public function setPosition(?int $position): self;
}
