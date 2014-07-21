<?php

namespace Ftrrtf\RollbarBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ftrrtf_rollbar');

        $rootNode
            ->children()
                ->arrayNode('notifier')
                    ->children()
                        ->scalarNode('batched')->defaultFalse()->end()
                        ->scalarNode('batch_size')->defaultValue('50')->end()
                        ->arrayNode('transport')
                            ->children()
                                ->scalarNode('type')->defaultValue('curl')->end()
                                ->scalarNode('access_token')->end()
                                ->scalarNode('agent_log_location')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('environment')
                    ->children()
//                        ->scalarNode('host')->defaultNull()->end()
                        ->scalarNode('branch')->defaultValue('master')->end()
                        ->scalarNode('root_dir')->defaultValue('')->end()
                        ->scalarNode('environment')->defaultValue('unknown')->end()
                        ->scalarNode('framework')->end()
                    ->end()
                ->end()
            ->end();


        return $treeBuilder;
    }
}
