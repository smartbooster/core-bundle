<?php

namespace Smart\CoreBundle\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

enum ProcessStatusEnum: string
{
    public const PREFIX_LABEL = 'enum.process_status.';

    case ONGOING = 'ongoing';
    case SUCCESS = 'success';
    case ERROR = 'error';

    public static function casesByTrans(TranslatorInterface $translator, bool $onlyValue = false, string $translationDomain = 'enum'): array
    {
        $toReturn = [];
        foreach (self::cases() as $case) {
            $toReturn[$translator->trans(self::PREFIX_LABEL . $case->value, [], $translationDomain)] = $onlyValue ? $case->value : $case;
        }

        return $toReturn;
    }
}
