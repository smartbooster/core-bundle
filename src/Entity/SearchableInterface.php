<?php

namespace Smart\CoreBundle\Entity;

/**
 * Allows to automatically add search update treatments in managers
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
interface SearchableInterface
{
    public function getSearch(): ?string;

    public function setSearch(?string $search): self;

    /**
     * returns the calculated value of the search
     * method not provided by the SearchableTrait to be defined
     * also used in fixtures to simplify the init of the search value
     */
    public function getComputedSearch(): string;
}
