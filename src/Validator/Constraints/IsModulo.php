<?php

namespace Smart\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IsModulo extends Constraint
{
    public string $message = 'is_modulo.error';

    public int $modulo;

    public function __construct(
        int $modulo,
        mixed $options = null,
        array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);

        $this->modulo = $modulo;
    }
}
