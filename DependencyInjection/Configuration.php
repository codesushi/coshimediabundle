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
            ->arrayNode('uploader')
                ->addDefaultsIfNotSet()

                ->children()
                ->scalarNode('media_path')
                    ->defaultValue('media')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('www_root')
                   ->defaultValue('web')->end()
                ->end()->end()
            ->scalarNode('media_class')
                ->isRequired()
                ->cannotBeEmpty()
                ->end()
        ->end();

        return $treeBuilder;
    }
}
