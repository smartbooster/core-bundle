<?php

namespace Smart\CoreBundle\Tests\Utils;

use Smart\CoreBundle\Utils\FileUtils;
use PHPUnit\Framework\TestCase;

/**
 * vendor/bin/simple-phpunit tests/Utils/FileUtilsTest.php
 */
class FileUtilsTest extends TestCase
{
    /**
     * @dataProvider slugifyFilenameProvider
     */
    public function testSlugifyFilename(string $expected, string $filename): void
    {
        $this->assertSame($expected, FileUtils::slugifyFilename($filename));
    }

    public function slugifyFilenameProvider(): array
    {
        return [
            'basic' => [
                // expected
                'smartcore.pdf',
                // filename
                'smartcore.pdf',
            ],
            'with_ascii_issue' => [
                // expected
                'smartcore.pdf',
                // filename
                'smàrtcôre.pdf',
            ],
            'with_space' => [
                // expected
                'smart-core.pdf',
                // filename
                'smart core.pdf',
            ],
            'without_extension' => [
                // expected
                'smartcore',
                // filename
                'smartcôre',
            ],
        ];
    }
}
