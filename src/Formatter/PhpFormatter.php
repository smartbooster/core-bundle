<?php

namespace Smart\CoreBundle\Formatter;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class PhpFormatter
{
    // MDT Native php format to use on DateTime::format https://www.php.net/manual/en/datetime.format.php
    public const DATE = 'Y-m-d';
    public const DATE_FR = 'd/m/Y';
    public const DATETIME = 'Y-m-d H:i';
    public const DATETIME_FR = 'd/m/Y H:i';
    public const DATETIME_WITH_SECONDS = 'Y-m-d H:i:s';
    public const DATETIME_WITH_SECONDS_FR = 'd/m/Y H:i:s';
}
