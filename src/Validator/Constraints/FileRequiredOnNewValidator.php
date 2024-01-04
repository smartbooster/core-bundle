<?php

namespace Smart\CoreBundle\Validator\Constraints;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class FileRequiredOnNewValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof FileRequiredOnNew) {
            throw new UnexpectedTypeException($constraint, FileRequiredOnNew::class);
        }

        $object = $this->context->getObject();
        // Need symfony/form dependency
        if ($object instanceof FormInterface) { // MDT adjust if use the contraints from collection
            $object = $object->getParent()->getData();
        }

        if ($object !== null && $object->getId() === null && $object->getFile() === null) {
            $this->context->buildViolation($constraint->message)
                ->atPath('file')
                ->addViolation();
        }
    }
}
