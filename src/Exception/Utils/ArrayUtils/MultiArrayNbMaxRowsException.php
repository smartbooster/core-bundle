<?php

namespace Smart\CoreBundle\Exception\Utils\ArrayUtils;

use Symfony\Component\HttpFoundation\Response;

class MultiArrayNbMaxRowsException extends \Exception
{
    /** @var int stock the maximum number of lines allowed */
    public int $nbMaxRows;

    /** @var int stock the current number of lines */
    public int $nbRows;

    public function __construct(int $nbMaxRows, int $nbRows)
    {
        $this->nbMaxRows = $nbMaxRows;
        $this->nbRows = $nbRows;

        parent::__construct(
            'utils.array.multi_array_nb_max_rows_error',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
