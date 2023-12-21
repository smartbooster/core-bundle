<?php

namespace Smart\CoreBundle\Exception\Utils\ArrayUtils;

use Symfony\Component\HttpFoundation\Response;

class MultiArrayNbFieldsException extends \Exception
{
    /** @var array List of MultiArray keys where the error occurred */
    public array $keys = [];

    public function __construct(array $keys)
    {
        $this->keys = $keys;

        parent::__construct(
            'utils.array.multi_array_nb_fields_error',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
