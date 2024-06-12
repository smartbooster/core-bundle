<?php

namespace Smart\CoreBundle\Utils;

use function Symfony\Component\String\u;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class MarkdownUtils
{
    /**
     * @param string $md '# Heading #{"id": "heading"}'
     * @param string $baseUrl 'http://url.test'
     * @return string '# <a href="http://url.test#heading" id="heading">Heading</a>'
     */
    public static function addAnchorToHeadings(string $md, string $baseUrl): string
    {
        foreach (u($md)->match('/# .* #{.*}/', PREG_PATTERN_ORDER)[0] as $heading) {
            $lastHashIndex = u($heading)->indexOfLast('#');
            $text = u($heading)->slice(1, $lastHashIndex - 1)->toString();
            $data = json_decode(u($heading)->slice($lastHashIndex + 1)->toString(), true);
            $id = $data['id'] ?? null;
            if ($id === null) {
                continue;
            }
            $anchoredHeading = sprintf(
                '# <a href="%s" id="%s">%s</a>',
                $baseUrl . '#' . $id,
                $id,
                trim($text),
            );
            $md = u($md)->replace($heading, $anchoredHeading)->toString();
        }

        return $md;
    }
}
