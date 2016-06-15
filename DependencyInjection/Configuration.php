<?php

namespace Rz\AdvancePageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('rz_advance_page');
        $this->addBundleSettings($node);
        $this->addConsumerSection($node);
        return $treeBuilder;
    }

      /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addBundleSettings(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('processor')->defaultValue('Rz\\AdvancePageBundle\\Processor\\Model\\NewsPageProcessor')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('settings')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('search')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('processor')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('service')->cannotBeEmpty()->end()
                                    ->end()  #--end processor children
                                ->end() #--end processor
                                ->arrayNode('config')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('identifier')->cannotBeEmpty()->end()
                                    ->end()  #--end config children
                                ->end() #--config category
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

     /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addConsumerSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('consumer_class')
                    ->children()
                        ->scalarNode('create_snapshots')->end()
                        ->scalarNode('create_snapshot')->end()
                        ->scalarNode('cleanup_snapshots')->end()
                        ->scalarNode('cleanup_snapshot')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
