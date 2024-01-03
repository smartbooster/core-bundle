<?php

namespace Smart\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validation;

class EmailChainValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof EmailChain) {
            throw new UnexpectedTypeException($constraint, EmailChain::class);
        }

        if ($value === null) {
            return;
        }

        $emailToValidate = explode($constraint->separator, $value);
        $validator = Validation::createValidator();
        foreach ($emailToValidate as $email) {
            // MDT add strict mode https://github.com/symfony/symfony/issues/35307
            $violations = $validator->validate(trim($email), [new Email(['mode' => Email::VALIDATION_MODE_STRICT])]);
            if ($violations->count() > 0 || trim($email) === '') {
                $this->context->buildViolation($constraint->message)->addViolation();
                break;
            }
        }
    }
}
