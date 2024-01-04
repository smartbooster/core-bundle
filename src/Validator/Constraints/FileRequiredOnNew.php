<?php

namespace Smart\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class FileRequiredOnNew extends Constraint
{
    public string $message = 'This value should not be blank.';
}
