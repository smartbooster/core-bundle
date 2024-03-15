<?php

namespace Smart\CoreBundle\Utils;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

use function Symfony\Component\String\u;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class StringUtils
{
    /**
     * input : App\Entity\Folder\SomeEntityName
     * output : folder_some_entity_name
     *
     * Used for security role
     */
    public static function getEntitySnakeName(string $entityClassName): string
    {
        return u(str_replace([
            'Proxies\__CG__\\',
            'App\Entity\\',
            '\\',
        ], [
            '',
            '',
            '_',
        ], $entityClassName))->snake()->toString();
    }

    /**
     * input : App\Entity\Folder\SomeEntityName
     * output : some_entity_name
     *
     * Used for translations
     */
    public static function getEntityShortName(string $entityClassName, bool $snakeCase = true): string
    {
        $toReturn = u(
            substr($entityClassName, strrpos($entityClassName, '\\') + 1)
        )->snake()->toString();
        if ($snakeCase) {
            return $toReturn;
        }

        return self::transformSnakeCaseToCamelCase($toReturn);
    }

    /**
     * input : App\Entity\User\Administrator
     * output : admin_user_administrator_
     */
    public static function getEntityRoutePrefix(string $entityClassName, string $context = 'admin'): string
    {
        return $context . '_' . self::getEntitySnakeName($entityClassName) . '_';
    }

    /**
     * MDT This method is more precise than a simple substr_count($string, PHP_EOL) because it takes into account the multi lignes of CSV
     */
    public static function getNbRowsFromTextarea(mixed $string, string $delimiter): int
    {
        $i = 0;
        $stream = $string;
        if (gettype($string) !== 'resource') {
            $stream = fopen("php://temp", "r+");
            if ($stream === false) {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'ArrayUtils::getNbRowsFromTextarea stream failure');
            }
            fwrite($stream, $string);
            rewind($stream); // Reset the stream pointer to the beginning
        }
        while (fgetcsv($stream, null, $delimiter) !== false) {
            $i++;
        }
        rewind($stream);
        if (gettype($string) !== 'resource') {
            fclose($stream);
        }

        return $i;
    }

    public static function encodeNewLine(?string $string): ?string
    {
        if ($string === null) {
            return null;
        }

        return str_replace(["\r\n", PHP_EOL], ['\\r\\n', '\\r\\n'], $string);
    }

    public static function decodeNewLine(?string $string): ?string
    {
        if ($string === null) {
            return null;
        }

        return str_replace(['\\r\\n'], ["\r\n"], $string);
    }

    public static function transformSnakeCaseToCamelCase(?string $string): ?string
    {
        if ($string === null) {
            return null;
        }

        $toReturn = str_replace('_', '', ucwords($string, '_'));
        $toReturn[0] = strtolower($toReturn[0]);

        return $toReturn;
    }

    public static function intToExcelColumn(int $n): string
    {
        $result = '';

        while ($n > 0) {
            $n--; // Decrement $n to match Excel's 1-based indexing
            $remainder = $n % 26;
            $result = chr(65 + $remainder) . $result;
            $n = (int)($n / 26);
        }

        return $result;
    }
}
