<?php

namespace Smart\CoreBundle\Utils;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class RequestUtils
{
    public static function getContextFromHost(string $host, ?string $domain = null): string
    {
        if ($host === $domain) {
            return 'app';
        }

        // MDT fallback au cas où il n'y a pas de sous domaine et que l'host est différent du domaine
        $toReturn = 'app';
        if (
            str_starts_with($host, 'app.') ||
            str_starts_with($host, 'admin.') ||
            str_starts_with($host, 'api.') ||
            str_starts_with($host, 'extranet.') ||
            substr_count($host, '.') > 1
        ) {
            $toReturn = substr($host, 0, (int) strpos($host, '.'));
        }

        return $toReturn;
    }
}
