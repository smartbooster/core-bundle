<?php

namespace Smart\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('smart_core');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                // MDT config to handle which API route is allowed to be restarted by the ApiCallMonitor
                ->arrayNode('monitoring_api_restart_allowed_routes')
                    ->prototype('scalar')->end()
                    ->defaultValue([])
                ->end()
                ->append($this->getEntityCleanupCommandConfigsDefinition())
            ->end()
        ;

        return $treeBuilder;
    }

    private function getEntityCleanupCommandConfigsDefinition(): ArrayNodeDefinition
    {
        return (new TreeBuilder('entity_cleanup_command_configs'))->getRootNode()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('older_than')->isRequired()->end()
                    ->scalarNode('older_than_property')->defaultValue('startedAt')->end()
                    ->scalarNode('class')->defaultValue('cron')->end()
                    ->scalarNode('where')->defaultNull()->end()
                    ->arrayNode('properties_to_clean')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
