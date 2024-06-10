<?php

namespace Smart\CoreBundle\DependencyInjection;

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
            ->end()
        ;

        return $treeBuilder;
    }
}
