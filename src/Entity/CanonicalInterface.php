<?php

namespace Smart\CoreBundle\Entity;

/**
 * Interface to facilitate the unique representation of an entity via its canonical.
 *
 * Technical documentation:
 *  - Once implemented on the entity, use its trait together.
 *  - Add the UniqueEntity attribute to display the error in the form:
 *      #[UniqueEntity(
 *          fields: 'canonical',
 *          errorPath: '%name of the target field where the error will be displayed%',
 *          message: '%Explicit message that explains on which field the canonical detection applies.%'
 *      )]
 *      or if you use annotation
 *      @UniqueEntity(fields={"canonical"}, errorPath="targetField", message="...")
 *  - Define the generateCanonical method. Recommendation: use an AsciiSlugger if it must deal with cases and without accents.
 *
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
interface CanonicalInterface
{
    public function getCanonical(): ?string;

    public function setCanonical(?string $canonical): static;

    /**
     * Generates the canonical from the entity, the content must be defined by the developer integrating the interface.
     * Can also be used in fixtures to simplify value init
     */
    public function generateCanonical(): string;
}
