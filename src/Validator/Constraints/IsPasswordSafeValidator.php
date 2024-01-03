<?php

namespace Smart\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * https://site.infocom94.fr/_attachment/articles-mars-2016-actualite-3-3/NP_MDP_NoteTech.pdf
 */
class IsPasswordSafeValidator extends ConstraintValidator
{
    protected const MINIMAL_STRING_LENGTH = 10;
    protected const COINTANS_LOWER_CHARACTER_REGEX = '/[a-z]+/';
    protected const COINTANS_UPPER_CHARACTER_REGEX = '/[A-Z]+/';
    protected const COINTANS_NUMBER_REGEX = '/[0-9]+/';

    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof IsPasswordSafe) {
            throw new UnexpectedTypeException($constraint, IsPasswordSafe::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (strlen($value) < self::MINIMAL_STRING_LENGTH) {
            $this->context->buildViolation($constraint->lengthMessage)->addViolation();
        }

        if (!preg_match(self::COINTANS_LOWER_CHARACTER_REGEX, $value, $matches)) {
            $this->context->buildViolation($constraint->missingLowerCharacterMessage)->addViolation();
        }

        if (!preg_match(self::COINTANS_UPPER_CHARACTER_REGEX, $value, $matches)) {
            $this->context->buildViolation($constraint->missingUpperCharacterMessage)->addViolation();
        }

        if (!preg_match(self::COINTANS_NUMBER_REGEX, $value, $matches)) {
            $this->context->buildViolation($constraint->missingNumberMessage)->addViolation();
        }
    }
}
