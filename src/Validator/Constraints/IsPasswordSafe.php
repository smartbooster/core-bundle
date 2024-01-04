<?php

namespace Smart\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IsPasswordSafe extends Constraint
{
    public string $lengthMessage = 'is_password_safe.length_error';
    public string $missingLowerCharacterMessage = 'is_password_safe.missing_lower_character_error';
    public string $missingUpperCharacterMessage = 'is_password_safe.missing_upper_character_error';
    public string $missingNumberMessage = 'is_password_safe.missing_number_error';
}
