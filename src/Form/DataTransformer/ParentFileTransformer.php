<?php

namespace Smart\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ParentFileTransformer implements DataTransformerInterface
{
    private mixed $parent;

    public function __construct(mixed $parent)
    {
        $this->parent = $parent;
    }

    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        if ($value !== null && $value->getParent() === null) {
            $value->setParent($this->parent);
        }

        return $value;
    }
}
