<?php

namespace Smart\CoreBundle\Utils;

use Smart\CoreBundle\Exception\Utils\ArrayUtils\MultiArrayNbFieldsException;
use Smart\CoreBundle\Exception\Utils\ArrayUtils\MultiArrayNbMaxRowsException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ArrayUtils
{
    /**
     * Convert and clean data from textarea to array
     */
    public static function getArrayFromTextarea(?string $string): array
    {
        if ($string === null) {
            return [];
        }

        $toReturn = explode(PHP_EOL, $string);
        $toReturn = array_map(function ($row) {
            return trim($row);
        }, $toReturn);

        $toReturn = array_unique(
            array_filter($toReturn, function ($value) {
                return strlen($value) != 0;
            })
        );

        return array_values($toReturn);
    }

    /**
     * Convert and clean data from textarea to a multidimensional array
     *
     * @param string $string The textarea value
     * @param string $delimiter
     * @param array $fields
     * @param ?int $nbMaxRows (Optional) Maximum number of lines allowed in the conversion from string to array
     * @return array
     */
    public static function getMultiArrayFromTextarea(string $string, string $delimiter, array $fields = [], int $nbMaxRows = null): array
    {
        $nbRows = StringUtils::getNbRowsFromTextarea($string, $delimiter);
        if (empty($fields)) { // We remove the header line from the nbRows if we are in dynamic mode
            $nbRows--;
        }
        if ($nbMaxRows != null && $nbRows > $nbMaxRows) {
            throw new MultiArrayNbMaxRowsException($nbMaxRows, $nbRows);
        }

        $stream = fopen("php://temp", "r+");
        if ($stream === false) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'ArrayUtils::getMultiArrayFromTextarea stream failure');
        }
        fwrite($stream, $string);
        rewind($stream); // Reset the stream pointer to the beginning

        $toReturn = [];
        $rowIndex = 0;
        $nbFields = count($fields);
        $nbFieldsErrorKeys = [];
        while (($row = fgetcsv($stream, null, $delimiter)) !== false) {
            // If no fields defined then the first header line becomes the fields
            if ($nbFields === 0) {
                $fields = array_map(function ($value) {
                    return trim($value);
                }, $row);
                $nbFields = count($fields);
                $rowIndex++;
                continue;
            }

            // Test of the number of values for the current row (+ skip if empty row)
            $nbRowValues = count($row);
            if ($nbRowValues != $nbFields && $nbRowValues === 1 && trim($row[0]) === '') {
                $rowIndex++;
                continue;
            } elseif ($nbRowValues != $nbFields) {
                $nbFieldsErrorKeys[] = $rowIndex + 1; // MDT stored human readable error index
                $rowIndex++;
                continue;
            }

            $toReturn[$rowIndex] = array_combine($fields, array_map(function ($value) {
                $value = trim($value);
                if ($value === '') {
                    $value = null;
                }

                return $value;
            }, $row));
            $rowIndex++;
        }
        fclose($stream);

        if (!empty($nbFieldsErrorKeys)) {
            throw new MultiArrayNbFieldsException($nbFieldsErrorKeys);
        }

        return $toReturn;
    }

    /**
     * @param array $input
     *  [
     *      0 => "boo,bar",
     *      1 => "dummy",
     *  ]
     * @return array
     *  [
     *      0 => "boo",
     *      1 => "bar",
     *      2 => "dummy",
     *  ]
     */
    public static function flattenArrayValues(array $input, string $separator = ','): array
    {
        $toReturn = [];

        foreach ($input as $value) {
            if (str_contains($value, $separator)) {
                $items = explode($separator, $value); // @phpstan-ignore-line
                foreach ($items as $item) {
                    array_push($toReturn, $item);
                }
            } else {
                array_push($toReturn, $value);
            }
        }

        return $toReturn;
    }
}
