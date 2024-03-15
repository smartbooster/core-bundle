<?php

namespace Smart\CoreBundle\Tests\Utils;

use Smart\CoreBundle\Utils\StringUtils;
use PHPUnit\Framework\TestCase;

/**
 * vendor/bin/simple-phpunit tests/Utils/StringUtilsTest.php
 */
class StringUtilsTest extends TestCase
{
    public function testGetEntitySnakeName(): void
    {
        $this->assertEquals('dummy', StringUtils::getEntitySnakeName('App\Entity\Dummy'));
        $this->assertEquals('context_foo', StringUtils::getEntitySnakeName('App\Entity\Context\Foo'));
        $this->assertEquals('context_foo_bar', StringUtils::getEntitySnakeName('App\Entity\Context\FooBar'));
        $this->assertEquals('context_sub_folder_foo_bar', StringUtils::getEntitySnakeName('App\Entity\Context\SubFolder\FooBar'));
        $this->assertEquals('prospect_organization', StringUtils::getEntitySnakeName('Proxies\__CG__\App\Entity\Prospect\Organization'));
    }

    public function testGetEntityShortName(): void
    {
        $this->assertEquals('dummy', StringUtils::getEntityShortName('App\Entity\Dummy'));
        $this->assertEquals('foo', StringUtils::getEntityShortName('App\Entity\Context\Foo'));
        $this->assertEquals('foo_bar', StringUtils::getEntityShortName('App\Entity\Context\FooBar'));
        $this->assertEquals('foo_bar', StringUtils::getEntityShortName('App\Entity\Context\SubFolder\FooBar'));
        $this->assertEquals('foo_bar', StringUtils::getEntityShortName('App\Entity\Context\SubFolder\FooBar'));
        $this->assertEquals('fooBar', StringUtils::getEntityShortName('App\Entity\Context\SubFolder\FooBar', false));
    }

    public function testGetEntityRoutePrefix(): void
    {
        $this->assertEquals('admin_dummy_', StringUtils::getEntityRoutePrefix('App\Entity\Dummy'));
        $this->assertEquals('admin_context_foo_', StringUtils::getEntityRoutePrefix('App\Entity\Context\Foo'));
        $this->assertEquals('admin_context_foo_bar_', StringUtils::getEntityRoutePrefix('App\Entity\Context\FooBar'));
        $this->assertEquals('admin_context_ca_range_', StringUtils::getEntityRoutePrefix('App\Entity\Context\CARange'));
        // Test case with other context
        $this->assertEquals('app_dummy_', StringUtils::getEntityRoutePrefix('App\Entity\Dummy', 'app'));
        $this->assertEquals('extranet_dummy_', StringUtils::getEntityRoutePrefix('App\Entity\Dummy', 'extranet'));
    }

    /**
     * @dataProvider getNbRowsFromTextareaProvider
     */
    public function testGetNbRowsFromTextarea(int $expected, string $values): void
    {
        $this->assertEquals($expected, StringUtils::getNbRowsFromTextarea($values, ';'));
    }

    public function getNbRowsFromTextareaProvider(): array
    {
        return [
            'Case 1 row' => [
                1,
                "foo;bar"
            ],
            'Case 1 split row with quote encapsulation' => [
                1,
                '"foo
                bar"; other'
            ],
            'Multi row split case with quote encapsulation' => [
                2,
                '"foo
                bar"; other
                aaa;bb'
            ],
            'Multi-row case without encapsulation' => [
                3,
                "some
                exemple
                text"
            ],
            'Case empty row' => [
                0,
                ""
            ],
        ];
    }

    /**
     * @dataProvider encodeNewLineProvider
     */
    public function testEncodeNewLine(?string $expected, ?string $values): void
    {
        $this->assertEquals($expected, StringUtils::encodeNewLine($values));
    }

    public function encodeNewLineProvider(): array
    {
        return [
            'multiple_rows' => [
                // expected
                'some\r\nexemple\r\ntext',
                // values
                "some
exemple
text"
            ],
            'space_line_row' => [
                'some\r\n\r\nexemple\r\n\r\ntext',
                "some

exemple

text"
            ],
            'no_new_line' => [
                "test word",
                "test word"
            ],
            'null' => [null, null],
        ];
    }

    /**
     * @dataProvider decodeNewLineProvider
     */
    public function testDecodeNewLine(?string $expected, ?string $values): void
    {
        $this->assertEquals($expected, StringUtils::decodeNewLine($values));
    }

    public function decodeNewLineProvider(): array
    {
        return [
            'multiple_rows' => [
                "some\r
exemple\r
text",
                'some\r\nexemple\r\ntext',
            ],
            'space_line_row' => [
                "some\r
\r
exemple\r
\r
text",
                'some\r\n\r\nexemple\r\n\r\ntext',
            ],
            'no_new_line' => [
                "test word",
                "test word",
            ],
            'null' => [null, null],
        ];
    }

    /**
     * @dataProvider transformSnakeCaseToPascalCaseProvider
     */
    public function testTransformSnakeCaseToPascalCase(string $expected, string $values): void
    {
        $this->assertEquals($expected, StringUtils::transformSnakeCaseToCamelCase($values));
    }

    public function transformSnakeCaseToPascalCaseProvider(): array
    {
        return [
            'foo_bar' => [
                // expected
                'fooBar',
                // values
                "foo_bar"
            ],
            'foo_bar_jon_doe' => [
                // expected
                'fooBarJonDoe',
                // values
                "foo_bar_jon_doe"
            ],
            'exemple1_2' => [
                // expected
                'exemple12',
                // values
                "exemple1_2"
            ],
        ];
    }

    /**
     * @dataProvider intToExcelColumnProvider
     */
    public function testIntToExcelColumn(string $expected, int $n): void
    {
        $this->assertEquals($expected, StringUtils::intToExcelColumn($n));
    }

    public function intToExcelColumnProvider(): array
    {
        return [
            'lower 0' => ['', -1],
            '0' => ['', 0],
            'lower to 26' => ['A', 1],
            'last letter 26' => ['Z', 26 ],
            'after the 26 letter' => ['AA', 27],
            'double alphabetical' => ['AZ', 52],
            'past double alphabetical' => ['BA', 53],
        ];
    }
}
