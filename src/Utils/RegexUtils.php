<?php

namespace Smart\CoreBundle\Utils;

class RegexUtils
{
    public const PHONE_PATTERN = '#^(\+[0-9]{2})?[0-9]{10}$#';
    public const PHONE_MESSAGE = 'phone.regex_error';
    public const FAX_MESSAGE = 'fax.regex_error';

    public const POSTAL_CODE_PATTERN = '#^(?!00\d{3}|0000)\d{4,5}$#';
    public const POSTAL_CODE_MESSAGE = 'postal_code.regex_error';

    public const SIRET_PATTERN = '#^\d{14}$#';
    public const SIRET_MESSAGE = 'siret.regex_error';

    public const SIREN_PATTERN = '#^\d{9}$#';
    public const SIREN_MESSAGE = 'siren.regex_error';
}
