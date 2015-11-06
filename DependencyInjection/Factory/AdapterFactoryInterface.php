<?php

namespace Coshi\MediaBundle\DependencyInjection\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

interface AdapterFactoryInterface
{
    public function create(ContainerBuilder $container, $id, array $config);

    public function getKey();

    public function addConfiguration(NodeDefinition $node);
}