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

    /**
     * @dataProvider checkIssetKeysProvider
     */
    public function testCheckIssetKeys(bool $expected, array $array, array $keys): void
    {
        $this->assertEquals($expected, ArrayUtils::checkIssetKeys($array, $keys));
    }

    public function checkIssetKeysProvider(): array
    {
        return [
            'all present' => [
                // expected
                true,
                // array
                [
                    0 => "000",
                    1 => "111",
                    5 => "555",
                ],
                // $keys
                [0, 1, 5],
            ],
            'one missing' => [
                // expected
                true,
                // array
                [
                    0 => "000",
                    1 => "111",
                    5 => "555",
                ],
                // $keys
                [0, 1],
            ],
            'key is not present' => [
                // expected
                false,
                // array
                [
                    'dummy' => "dummy",
                    'foo' => "foo",
                ],
                // $keys
                ['foo', 'bar'],
            ],
            'empty array' => [
                // expected
                true,
                // array
                [],
                // $keys
                [],
            ],
        ];
    }

    /**
     * @dataProvider getTrimExplodeProvider
     */
    public function testTrimExplode(array $expected, string $string, ?string $separator): void
    {
        if ($separator === null) {
            $this->assertEquals($expected, ArrayUtils::trimExplode($string));
        } else {
            $this->assertEquals($expected, ArrayUtils::trimExplode($string, $separator));
        }
    }

    public function getTrimExplodeProvider(): array
    {
        return [
            'empty string' => [
                [''],
                '',
                null
            ],
            'one_value' => [
                ['value'],
                ' value ',
                null
            ],
            'multiple_value' => [
                ['value', 'dummy@email.fr', '8'],
                ' value , dummy@email.fr        , 8',
                null
            ],
            '| separator' => [
                ['value', 'dummy@email.fr'],
                ' value | dummy@email.fr',
                '|'
            ],
        ];
    }

    public function testTrimExplodeEmptySeparator(): void
    {
        $this->expectException(\RuntimeException::class);

        ArrayUtils::trimExplode('', '');
    }

    /**
     * @dataProvider getRemoveEmptyProvider
     */
    public function testRemoveEmpty(array $expected, array $values): void
    {
        $this->assertEquals($expected, ArrayUtils::removeEmpty($values));
    }

    public function getRemoveEmptyProvider(): array
    {
        return [
            'empty array' => [
                [],
                []
            ],
            'remove empty' => [
                [
                    1 => "some",
                    2 => "exemple",
                    4 => "text"
                ],
                ["", "some","exemple", "", "text", null, ""]
            ],
        ];
    }

    /**
     * @dataProvider getFilterByPatternProvider
     */
    public function testFilterByPattern(array $expected, array $values, string $pattern, bool $negate): void
    {
        $this->assertEquals($expected, ArrayUtils::filterByPattern($values, $pattern, $negate));
    }

    public function getFilterByPatternProvider(): array
    {
        return [
            'empty array' => [
                [],
                [],
                "/^([0-9]{9})$/",
                false
            ],
            'siren pattern' => [
                [
                    0 => "123456789",
                    3 => "147258369"
                ],
                ["123456789", "test", "123", "147258369", "12345678910111213"],
                "/^([0-9]{9})$/",
                false
            ],
            'siren pattern negated' => [
                [
                    1 => "test",
                    2 => "123",
                    4 => "12345678910111213"
                ],
                ["123456789", "test", "123", "147258369", "12345678910111213"],
                "/^([0-9]{9})$/",
                true
            ],
            'no match' => [
                [
                    0 => "test",
                    1 => "123",
                ],
                ["test", "123"],
                "/^([0-9]{9})$/",
                true
            ],
        ];
    }

    public function testFilterByPatternMalformedPattern(): void
    {
        $this->expectWarning();

        ArrayUtils::filterByPattern([0 => 0], '#######');
    }

    /**
     * @dataProvider getFlatToMapProvider
     */
    public function testFlatToMap(array $expected, ?array $array, ?\Closure $fnKey, ?\Closure $fnValue): void
    {
        $this->assertEquals($expected, ArrayUtils::flatToMap($array, $fnKey, $fnValue));
    }

    public function getFlatToMapProvider(): array
    {
        return [
            'null array' => [
                [],
                null,
                function (?int $value) {
                    return $value;
                },
                function (?int $value) {
                    return $value;
                },
            ],
            'empty array' => [
                [],
                [],
                function (?int $value) {
                    return $value;
                },
                function (?int $value) {
                    return $value;
                },
            ],
            'key_value' => [
                [2 => 3, 4 => 6, 6 => 9],
                [1, 2, 3],
                function (int $value) {
                    return $value * 2;
                },
                function (int $value) {
                    return $value * 3;
                },
            ],
            'only key' => [
                [5 => 1, 10 => 2, 15 => 3],
                [1, 2, 3],
                function (int $value) {
                    return $value * 5;
                },
                null,
            ],
            'only value' => [
                [1 => 4, 2 => 8, 3 => 12],
                [1, 2, 3],
                null,
                function (int $value) {
                    return $value * 4;
                },
            ],
        ];
    }

    /**
     * @dataProvider hasDuplicateProvider
     */
    public function testHasDuplicateValue(bool $expected, array $array): void
    {
        $this->assertSame($expected, ArrayUtils::hasDuplicateValue($array));
    }

    public function hasDuplicateProvider(): array
    {
        return [
            'array_with_string_duplicated' => [
                true,
                ['a', 'b', 'c', 'a']
            ],
            'array_with_string_not_duplicated' => [
                false,
                ['a', 'b', 'c']
            ],
            'array_with_int_duplicated' => [
                true,
                [1, 15, 15, 50]
            ],
            'array_with_int_not_duplicated' => [
                false,
                [18, 10, 88]
            ],
        ];
    }

    /**
     * @dataProvider toIndexedArrayProvider
     */
    public function testToIndexedArray(array $expected, array $array): void
    {
        $this->assertSame($expected, ArrayUtils::toIndexedArray($array));
    }

    public function toIndexedArrayProvider(): array
    {
        return [
            'simple_array_indexed' => [[1, 2, 3], ['3' => 1, '2' => 2, '1' => 3]],
            'simple_array_not_indexed' => [[1, 2, 3], [1, 2, 3]],
            'multidimensional_array_indexed' => [[[1, 2], [3, 4]], [['dummy' => 1, 'test' => 2], ['8' => 3, '1' => 4]]],
            'multidimensional_array_not_indexed' => [[[1, 2], [3, 4]], [[1, 2], [3, 4]]],
        ];
    }
}
