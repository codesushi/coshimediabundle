<?php

namespace Coshi\MediaBundle\DependencyInjection\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;


class LocalAdapterFactory implements AdapterFactoryInterface
{
    public function create(ContainerBuilder $container, $id, array $config)
    {   
        $container
            ->setDefinition($id, new DefinitionDecorator('coshi_media.adapter.local'))
            ->replaceArgument(0, $config['directory'])
            ->replaceArgument(1, $config['create'])
        ;
    }

    public function getKey()
    {
        return 'local';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('directory')->isRequired()->end()
                ->booleanNode('create')->defaultTrue()->end()
            ->end()
        ;
    }
}