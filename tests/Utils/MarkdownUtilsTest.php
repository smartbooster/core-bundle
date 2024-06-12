<?php

namespace Smart\CoreBundle\Tests\Utils;

use Smart\CoreBundle\Utils\MarkdownUtils;
use PHPUnit\Framework\TestCase;

/**
 * vendor/bin/simple-phpunit tests/Utils/MarkdownUtilsTest.php
 */
class MarkdownUtilsTest extends TestCase
{
    /**
     * @dataProvider addAnchorToHeadingsProvider
     */
    public function testAddAnchorToHeadings(string $expected, string $md, string $baseUrl): void
    {
        $this->assertEquals($expected, MarkdownUtils::addAnchorToHeadings($md, $baseUrl));
    }

    public function addAnchorToHeadingsProvider(): array
    {
        $baseUrl = 'http://url.test';

        return [
            'without heading' => [
                'Texte',
                'Texte',
                $baseUrl,
            ],
            'heading without anchor' => [
                '# Heading',
                '# Heading',
                $baseUrl,
            ],
            'heading with anchor' => [
                '# <a href="http://url.test#heading" id="heading">Heading</a>',
                '# Heading #{"id": "heading"}',
                $baseUrl,
            ],
            'heading with anchor + text before and after which need to be untouched after formatting' => [
                <<<EOD
Texte avant
# <a href="http://url.test#heading" id="heading">Heading</a>
Texte Après
EOD,
                <<<EOD
Texte avant
# Heading #{"id": "heading"}
Texte Après
EOD,
                $baseUrl,
            ],
            'anchored heading with a level superior to h1' => [
                <<<EOD
Texte avant
### <a href="http://url.test#heading" id="heading">Heading</a>
Texte Après
EOD,
                <<<EOD
Texte avant
### Heading #{"id": "heading"}
Texte Après
EOD,
                $baseUrl,
            ],
            'multiple headings with same level' => [
                <<<EOD
## <a href="http://url.test#heading-a" id="heading-a">Heading A</a>
## <a href="http://url.test#heading-b" id="heading-b">Heading B</a>
EOD,
                <<<EOD
## Heading A #{"id": "heading-a"}
## Heading B #{"id": "heading-b"}
EOD,
                $baseUrl,
            ],
            'multiple headings with different level' => [
                <<<EOD
# <a href="http://url.test#heading-1" id="heading-1">Heading 1</a>
## <a href="http://url.test#heading-2" id="heading-2">Heading 2</a>
### <a href="http://url.test#heading-3" id="heading-3">Heading 3</a>
EOD,
                <<<EOD
# Heading 1 #{"id": "heading-1"}
## Heading 2 #{"id": "heading-2"}
### Heading 3 #{"id": "heading-3"}
EOD,
                $baseUrl,
            ],
        ];
    }
}
