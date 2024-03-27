<?php

namespace Smart\CoreBundle\Config;

/**
 * Generic service to handle php override for ini options (including memory)
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class IniOverrideConfig
{
    private string $defaultMemoryLimit;

    /**
     * @param ?string $batchMemory based on the SMART_CORE_BATCH_MEMORY env variable
     */
    public function __construct(protected ?string $batchMemory)
    {
        $this->defaultMemoryLimit = $this->getCurrentMemoryLimit();
    }

    private function getDefaultMemoryLimit(): string
    {
        return $this->defaultMemoryLimit;
    }

    public function getCurrentMemoryLimit(): string
    {
        return ini_get('memory_limit');
    }

    public function increaseMemoryLimit(): void
    {
        if ($this->batchMemory === null || (int) $this->batchMemory <= (int) $this->getCurrentMemoryLimit()) {
            return;
        }
        ini_set('memory_limit', $this->batchMemory);
    }

    public function resetMemoryLimit(): void
    {
        ini_set('memory_limit', $this->getDefaultMemoryLimit());
    }

    /**
     * Call this function for all PHP script running from the CLI (ex: all symfony cron commands) when you are on CleverCloud to ensure every date
     * creation or comparaison are using the right timezone.
     * Alternativaly you can also set it directly when invoking the PHP command using the -d option like so :
     *  php -d date.timezone="Europe/Paris" bin/console app:my-command
     */
    public function initDefaultTimezoneForCli(string $timezone = 'Europe/Paris'): void
    {
        date_default_timezone_set($timezone);
    }
}
