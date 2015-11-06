<?php

namespace Coshi\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class Configuration implements ConfigurationInterface
{   
    protected $factories;

    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('coshi_media');
        
        $this->setMediaClassSection($rootNode);
        $this->setFilesystemSection($rootNode);
        $this->setAdaptersSection($rootNode, $this->factories);


        return $treeBuilder;
    }

    public function setMediaClassSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('media_class')
                ->isRequired()
                ->cannotBeEmpty()
            ->end();
    }

    public function setAdaptersSection(ArrayNodeDefinition $node, array $factories)
    {
        $adapterNode = $node
            ->children()
                ->arrayNode('adapters')
                    ->children();

        foreach ($factories as $name => $factory) {
            $factoryNode = $adapterNode->arrayNode($name)->canBeUnset();

            $factory->addConfiguration($factoryNode);
        }
        
        $adapterNode->end();   
    }

    public function setFilesystemSection(ArrayNodeDefinition $node)
    {
        $nodeBuilder = $node;
            
        $nodeBuilder
            ->children()
                ->scalarNode('filesystem_default')
            ->end();

        $nodeBuilder
            ->children()
                ->arrayNode('filesystems')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('adapter')->isRequired()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
