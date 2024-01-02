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
}
