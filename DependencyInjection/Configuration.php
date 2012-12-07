<?php

namespace Coshi\MediaBundle\DependencyInjection;

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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('coshi_media');
        $rootNode->children()
            /*
            ->arrayNode('linkmap')
                ->useAttributeAsKey('name')
                    ->prototype('scalar')
                ->end()
            ->end()
             */
            ->arrayNode('uploader')->children()
                ->scalarNode('media_path')
                ->defaultValue('media')->end()
                ->end()->end()
            ->scalarNode('media_class')
                ->isRequired()
                ->cannotBeEmpty()
                ->end()
            /*->arrayNode('imager')->isRequired()
                ->children()->arrayNode('options')->children()
                    ->scalarNode('lib')->defaultValue('gd')->end()
                    ->arrayNode('thumbnails')->useAttributeAsKey('name')
                                    ->prototype('array')
                                    ->useAttributeAsKey('name')
                                    ->prototype('scalar')->end()
                           ->end()
                        ->end()
                        ->end()*/
        ->end();


        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
