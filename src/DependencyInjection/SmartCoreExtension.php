<?php

namespace Smart\CoreBundle\DependencyInjection;

use Smart\CoreBundle\Command\EntityCleanupCommand;
use Smart\CoreBundle\Monitoring\ApiCallMonitor;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class SmartCoreExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $config = $this->processConfiguration(new Configuration(), $configs);
        $apiCallMonitor = $container->getDefinition(ApiCallMonitor::class);
        $apiCallMonitor->addMethodCall('setRestartAllowedRoutes', [$config['monitoring_api_restart_allowed_routes']]);

        $entityCleanupCommand = $container->getDefinition(EntityCleanupCommand::class);
        $entityCleanupCommand->addMethodCall('setCommandConfigs', [$config['entity_cleanup_command_configs']]);
    }
}
