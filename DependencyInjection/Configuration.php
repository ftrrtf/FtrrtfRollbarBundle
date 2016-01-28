<?php

namespace Ftrrtf\RollbarBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * FtrrtfRollbarExtension configuration structure.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ftrrtf_rollbar');

        $rootNode
            ->children()
                ->arrayNode('notifier')
                ->isRequired()
                    ->children()
                        ->arrayNode('server')
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
                        ->arrayNode('client')
                            ->children()
                                ->scalarNode('access_token')->end()
                                ->booleanNode('source_map_enabled')
                                    ->defaultFalse()
                                ->end()
                                ->scalarNode('code_version')
                                    ->defaultValue('')
                                ->end()
                                ->booleanNode('guess_uncaught_frames')
                                    ->defaultFalse()
                                ->end()
                                ->scalarNode('rollbarjs_version')
                                    ->defaultValue('v1')
                                ->end()
                                ->scalarNode('check_ignore_function_provider')
                                    ->defaultValue('ftrrtf_rollbar.check_ignore_function_provider.default')
                                ->end()
                                ->arrayNode('allowed_js_hosts')
                                    ->prototype('scalar')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('environment')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('branch')->defaultValue('master')->end()
                        ->scalarNode('root_dir')->defaultValue('')->end()
                        ->scalarNode('environment')->defaultValue('unknown')->end()
                        ->scalarNode('framework')->end()
                        ->scalarNode('code_version')->defaultValue('')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
