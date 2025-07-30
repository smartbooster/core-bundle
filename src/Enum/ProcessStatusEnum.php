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

    public static function getBgColor(mixed $case): ?string
    {
        return match ($case) {
            self::ONGOING => '#3b82f6', // info-color colors.blue.500
            self::SUCCESS => '#22c55e', // success-color colors.green.500
            self::ERROR => '#dc2626', // danger-color colors.red.600
            default => null,
        };
    }

    public static function getBgColors(): array
    {
        return array_map(function (self $case) {
            return self::getBgColor($case);
        }, self::cases());
    }

    public static function getTextColors(): array
    {
        return array_map(function (self $case) {
            return self::getTextColor($case);
        }, self::cases());
    }

    public static function getTextColor(mixed $case): ?string // @phpstan-ignore-line false positive can be null on override
    {
        return '#ffffff';
    }

    public static function labels(TranslatorInterface $translator): array
    {
        return array_map(function (self $case) use ($translator) {
            return $translator->trans(self::PREFIX_LABEL . $case->value, [], 'enum');
        }, self::cases());
    }
}
