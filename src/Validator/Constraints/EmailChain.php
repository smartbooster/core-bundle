<?php

namespace Smart\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class EmailChain extends Constraint
{
    public string $message = 'email_chain.format_error';

    /** @var non-empty-string */
    public string $separator;

    /**
     * @param non-empty-string $separator
     */
    public function __construct(
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null,
        string $separator = ','
    ) {
        parent::__construct($options, $groups, $payload);

        $this->separator = $separator;
    }
}
