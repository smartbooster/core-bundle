<?php

namespace Smart\CoreBundle\Tests\Utils;

use Smart\CoreBundle\Exception\Utils\ArrayUtils\MultiArrayNbFieldsException;
use Smart\CoreBundle\Exception\Utils\ArrayUtils\MultiArrayNbMaxRowsException;
use Smart\CoreBundle\Utils\ArrayUtils;
use PHPUnit\Framework\TestCase;

/**
 * vendor/bin/simple-phpunit tests/Utils/ArrayUtilsTest.php
 */
class ArrayUtilsTest extends TestCase
{
    /**
     * @dataProvider getArrayFromTextareaProvider
     */
    public function testArrayFromTextarea(array $expected, ?string $values): void
    {
        $this->assertEquals($expected, ArrayUtils::getArrayFromTextarea($values));
    }

    public function getArrayFromTextareaProvider(): array
    {
        return [
            'clean textarea' => [
                // expected
                ["some","text"],
                // values
                "some

                text"
            ],
            'no special char deleted' => [
                // expected
                ["some,","exemple;", "text*"],
                // values
                "some,
                exemple;
                text*"
            ],
            'null' => [
                // expected
                [],
                // values
                null
            ],
        ];
    }

    /**
     * @dataProvider getMultiArrayNbFieldsExceptionFromTextareaProvider
     * @param string $string
     * @param string $delimiter
     * @param array $fields
     * @param array $keys
     */
    public function testMultiArrayNbFieldsExceptionFromTextarea(string $string, string $delimiter, array $fields, array $keys): void
    {
        try {
            ArrayUtils::getMultiArrayFromTextarea($string, $delimiter, $fields);
        } catch (MultiArrayNbFieldsException $e) {
            $this->assertEquals($keys, $e->keys);
        }
    }

    public function getMultiArrayNbFieldsExceptionFromTextareaProvider(): array
    {
        return [
            'missing_field_data' => [
                "TEST_CODE_1,name
                TEST_CODE_2
                TEST_CODE_3,name
                TEST_CODE_4,name
                TEST_CODE_5",
                ",",
                ['code', 'name'],
                [2, 5],
            ],
            'missing_field_data_with_empty_header_param' => [
                "code,name
                TEST_CODE_1,name
                TEST_CODE_2
                TEST_CODE_3,name
                TEST_CODE_4,name
                TEST_CODE_5",
                ",",
                [],
                [3, 6],
            ],
            'additional_field_data' => [
                "TEST_CODE_1,NAME_1
                TEST_CODE_1,NAME_1,1",
                ",",
                ['code', 'name'],
                [2],
            ],
            'wrong_delimiter_causing_wrong_field_split' => [
                "TEST_CODE_1,NAME_1,1",
                ";",
                ['code', 'name', 'value'],
                [1],
            ],
        ];
    }

    /**
     * @dataProvider getMultiArrayNbMaxRowsExceptionProvider
     * @param string $string
     * @param string $delimiter
     * @param array $fields
     * @param int $nbMaxRows
     */
    public function testMultiArrayNbMaxRowsExceptionFromTextarea(string $string, string $delimiter, array $fields, int $nbMaxRows): void
    {
        try {
            ArrayUtils::getMultiArrayFromTextarea($string, $delimiter, $fields, $nbMaxRows);
        } catch (MultiArrayNbMaxRowsException $e) {
            $this->assertEquals($nbMaxRows, $e->nbMaxRows);
        }
    }

    public function getMultiArrayNbMaxRowsExceptionProvider(): array
    {
        return [
            'nb_max_rows_exception' => [
                "TEST_CODE_1,name
                TEST_CODE_2,name
                TEST_CODE_3,name",
                ",",
                ['code', 'name'],
                2
            ],
            'nb_max_rows_exception cas version multi ligne' => [
                'TEST_CODE_1,name
                TEST_CODE_2,"name
                sur 2 lignes"
                TEST_CODE_3,name', // ligne supplémentaire pour déclencher l'erreur cas la précédente correspond à la deuxième en multi ligne
                ",",
                ['code', 'name'],
                2
            ],
        ];
    }

    /**
     * @dataProvider getMultiArrayFromTextareaProvider
     * @param array $expectedMultiArray
     * @param string $string
     * @param string $delimiter
     * @param array $fields
     */
    public function testMultiArrayFromTextarea(array $expectedMultiArray, string $string, string $delimiter, array $fields = []): void
    {
        $this->assertEquals($expectedMultiArray, ArrayUtils::getMultiArrayFromTextarea($string, $delimiter, $fields));
    }

    public function getMultiArrayFromTextareaProvider(): array
    {
        $simpleString = "TEST_CODE_1;NAME_1;1
            TEST_CODE_2;NAME_2;2
            TEST_CODE_2;NAME_2;2
        ";

        $emptyLineString = "
            TEST_CODE_1;NAME_1;1
            
            TEST_CODE_2;NAME_2;2
        ";

        $quotedString = '"TEST_CODE_1","NAME_1","1"
            "TEST_CODE_2",,"2"
            "TEST_CODE_3","NAME_3",
        ';

        $quotedStringMultiLines = '"TEST_CODE_1","NAME
on

multiple
lines","1"
            "TEST_CODE_2","An other
name","2"';

        $quotedStringWithSpaces = '"TEST_CODE_1", "NAME_1", "1"
            "TEST_CODE_2" , "NAME_2" ,"2"
        ';

        $numberWithComma = '"TEST_CODE_1", "NAME_1", "1,1"
            "TEST_CODE_2" , "NAME_2" ,"2,2"
        ';

        return [
            'simpleString' => [
                // expected multi array
                [
                    ['code' => 'TEST_CODE_1', 'name' => 'NAME_1', 'value' => 1],
                    ['code' => 'TEST_CODE_2', 'name' => 'NAME_2', 'value' => 2],
                    ['code' => 'TEST_CODE_2', 'name' => 'NAME_2', 'value' => 2], // MDT ne clean plus les doublons
                ],
                // string
                $simpleString,
                // delimiter
                ";",
                // fields
                ['code', 'name', 'value']
            ],
            'emptyLineString' => [
                [
                    1 => ['code' => 'TEST_CODE_1', 'name' => 'NAME_1', 'value' => 1],
                    3 => ['code' => 'TEST_CODE_2', 'name' => 'NAME_2', 'value' => 2],
                ],
                $emptyLineString,
                ";",
                ['code', 'name', 'value']
            ],
            'quotedString' => [
                [
                    ['code' => 'TEST_CODE_1', 'name' => 'NAME_1', 'value' => 1],
                    ['code' => 'TEST_CODE_2', 'name' => null, 'value' => 2],
                    ['code' => 'TEST_CODE_3', 'name' => 'NAME_3', 'value' => null],
                ],
                $quotedString,
                ",",
                ['code', 'name', 'value']
            ],
            'quotedStringMultiLines' => [
                [
                    ['code' => 'TEST_CODE_1', 'name' => "NAME
on

multiple
lines", 'value' => 1],
                    ['code' => 'TEST_CODE_2', 'name' => 'An other
name', "value" => 2],
                ],
                $quotedStringMultiLines,
                ",",
                ['code', 'name', 'value']
            ],
            'quotedStringWithSpaces' => [
                [
                    ['code' => 'TEST_CODE_1', 'name' => 'NAME_1', 'value' => 1],
                    ['code' => 'TEST_CODE_2', 'name' => 'NAME_2', 'value' => 2],
                ],
                $quotedStringWithSpaces,
                ",",
                ['code', 'name', 'value']
            ],
            'numberWithComma' => [
                [
                    ['code' => 'TEST_CODE_1', 'name' => 'NAME_1', 'value' => '1,1'],
                    ['code' => 'TEST_CODE_2', 'name' => 'NAME_2', 'value' => '2,2'],
                ],
                $numberWithComma,
                ",",
                ['code', 'name', 'value']
            ],
            'conversion first row as header if no fields' => [
                // expected multi array
                [
                    // MDT La première ligne est enlevé donc on commance à 1
                    // permet de conserver le bon numéro de ligne si erreur de validation à remonter via l'index + 1
                    1 => ['head1' => 'foo_a', 'head2' => 'bar_a', 'head3' => 'dummy_a'],
                    2 => ['head1' => 'foo_b', 'head2' => 'bar_b', 'head3' => 'dummy_b'],
                ],
                // string
                'head1;head2;head3
                foo_a;bar_a;dummy_a
                foo_b;bar_b;dummy_b',
                // delimiter
                ";",
            ],
            'header override test with two identical headers (case without fields) ' => [
                // expected multi array
                [
                    1 => ['head1' => 'foo_a', 'head2' => 'dummy_a'],
                    2 => ['head1' => 'foo_b', 'head2' => 'dummy_b'],
                ],
                // string
                'head1;head2;head2
                foo_a;bar_a;dummy_a
                foo_b;bar_b;dummy_b',
                // delimiter
                ";",
            ],
            'header mode without additional data (case without fields) ' => [
                // expected multi array
                [],
                // string
                'head1;head2;head2',
                // delimiter
                ";",
            ],
        ];
    }

    /**
     * @dataProvider flattenArrayValuesProvider
     */
    public function testFlattenArrayValues(array $expected, array $input, string $separator): void
    {
        $this->assertEquals($expected, ArrayUtils::flattenArrayValues($input, $separator));
    }

    public function flattenArrayValuesProvider(): array
    {
        return [
            'simple case' => [
                // expected array
                [
                    0 => "foo",
                    1 => "bar",
                    2 => "dummy",
                ],
                // input array
                [
                    0 => "foo,bar",
                    1 => "dummy",
                ],
                // separator
                ",",
            ],
            'case with values without separator' => [
                [
                    0 => "foo",
                    1 => "bar",
                ],
                [
                    0 => "foo",
                    1 => "bar",
                ],
                ",",
            ],
        ];
    }
}
