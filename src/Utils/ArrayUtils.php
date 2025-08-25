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
     *
     * <pre>
     * <?php
     * getArrayFromTextarea("some\ntext");
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * ["some","text"]
     * </pre>
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
    public static function getMultiArrayFromTextarea(string $string, string $delimiter, array $fields = [], ?int $nbMaxRows = null): array
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
            if ($nbRowValues != $nbFields && $nbRowValues === 1 && trim($row[0] ?? '') === '') {
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
     * @return array
     * <pre>
     * <?php
     * flattenArrayValues([
     *      0 => "boo,bar",
     *      1 => "dummy",
     *  ]);
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * [
     *      0 => "boo",
     *      1 => "bar",
     *      2 => "dummy",
     *  ]
     * </pre>
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

    /**
     * Check if all keys are set in array
     * It's not a strict comparaison, all array values may not be in keys
     *
     * @param array $array array to compare
     * @param array $keys keys to check
     */
    public static function checkIssetKeys(array $array, array $keys): bool
    {
        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Explode and trim an array with separator
     *
     * <pre>
     * <?php
     * trimExplode("value_1 , value_2");
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * ['value_1', 'value_2']
     * </pre>
     */
    public static function trimExplode(string $string, string $separator = ','): array
    {
        if ($separator === '') {
            throw new \RuntimeException("separator must not be empty");
        }

        return array_map(function (string $element) {
            return trim($element);
        }, explode($separator, $string));
    }

    /**
     * Remove null and empty string from array
     */
    public static function removeEmpty(array $array): array
    {
        return array_filter($array, function ($value) {
            if (!is_object($value) && ($value === null || strlen($value) === 0)) {
                return false;
            }

            return true;
        });
    }

    /**
     * Filter array by search pattern
     * if pattern is malformed, PHPUnit\Framework\Error\Warning exception is thrown
     */
    public static function filterByPattern(array $array, string $pattern, bool $negate = false): array
    {
        return array_filter($array, function ($value) use ($pattern, $negate) {
            if ($negate) {
                return !preg_match($pattern, $value);
            }

            return (bool) preg_match($pattern, $value);
        });
    }

    /**
     * Transform an array into an associative array with key and value callback
     * Inspiration : https://szymonkrajewski.pl/building-the-associative-array-ideas/
     *
     * <pre>
     * <?php
     * flatToMap(
     * [1, 2],
     * function (int $e) {
     *    return $e * 2;
     * },
     * function (int $e) {
     *    return $e * 3;
     * }
     * );
     * ?>
     * </pre>
     * The above example will output:
     * <pre>
     * [2 => 3, 4 => 6]
     * </pre>
     */
    public static function flatToMap(?array $array, ?\Closure $fnKey, ?\Closure $fnValue = null): array
    {
        if (is_null($array)) {
            return [];
        }

        if (!is_null($fnKey)) {
            $keys = array_map($fnKey, $array);
        } else {
            $keys = $array;
        }

        if (!is_null($fnValue)) {
            $values = array_map($fnValue, $array);
        } else {
            $values = $array;
        }

        return array_combine($keys, $values);
    }

    /**
     * Inspiration : https://stackoverflow.com/questions/3145607/php-check-if-an-array-has-duplicates
     */
    public static function hasDuplicateValue(array $array): bool
    {
        return count($array) !== count(array_flip($array));
    }

    /**
     * Delete keys of array and multidimensional array
     *
     *  <pre>
     *  <?php
     *  toIndexedArray(['john' => 1, 'doe' => ['smart' => 100, 'booster' => 200]);
     *  ?>
     *  </pre>
     *  The above example will output:
     *  <pre>
     *  [1, [100, 200]]
     *  </pre>
     */
    public static function toIndexedArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::toIndexedArray($value);
            }
        }

        return array_values($array);
    }

    /**
     * Order an key value array like other array
     * @param array $arrayToSort The array to sort. It's sort by the key of every values.
     * @param array $arraySorted The array to use for sorting. The comparison is between values, not between keys.
     * @return array
     *  <pre>
     *  <?php
     *  sortArrayKeyByArray(['orange' => null, 'white' => null, 'blue' => null], ['blue', 'orange', 'white']);
     *  ?>
     *  </pre>
     *  The above example will output:
     *  <pre>
     *  ['blue' => null, 'orange' => null, 'white' => null]
     *  </pre>
     */
    public static function sortArrayKeyByArray(array $arrayToSort, array $arraySorted): array
    {
        return array_replace(array_intersect_key(array_flip($arraySorted), $arrayToSort), $arrayToSort);
    }
}
