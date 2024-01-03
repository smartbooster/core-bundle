<?php

namespace Smart\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IsModuloValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsModulo) {
            throw new UnexpectedTypeException($constraint, IsModulo::class);
        }

        if ($value != null && $value % $constraint->modulo !== 0) {
            $this->context->buildViolation($constraint->message, ['%modulo%' => $constraint->modulo])->addViolation();
        }
    }
}
