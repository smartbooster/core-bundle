<?php

namespace Smart\CoreBundle\Command;

use Smart\CoreBundle\Utils\ArrayUtils;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class CommandPoolHelper
{
    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    /**
     * Return all commands indexed by a given type
     * @return Command[]
     */
    public function getCommands(string $type): array
    {
        $application = new Application($this->kernel);

        return array_filter($application->all(), function ($key) use ($type) {
            return str_starts_with($key, "$type:");
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Parse commands from a given type and return an associative array that can be used in form choice type
     * @return array
     *  [
     *      'app.my_command.label' => 'my-command',
     *      'app.my_other_command.label' => 'my-other-command',
     *  ]
     */
    public function getCommandsChoices(string $type): array
    {
        return ArrayUtils::flatToMap(
            array_keys($this->getCommands($type)),
            function ($key) {
                return str_replace([':', '-'], ['.', '_'], $key) . '.label';
            },
            function ($value) use ($type) {
                return str_replace("$type:", '', $value);
            }
        );
    }

    public function getCronChoices(): array
    {
        return $this->getCommandsChoices('cron');
    }
}
