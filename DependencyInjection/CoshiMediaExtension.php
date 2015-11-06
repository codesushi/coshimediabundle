<?php

namespace Coshi\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class CoshiMediaExtension extends Extension
{   

    protected $adaptersFactories;
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(
            $this->getConfiguration($configs, $container),
            $configs
        );
        
        $loader = new Loader\XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.xml');
        
        $adapters = $this->getAdapters($container, $config, $this->adaptersFactories);

        //Create defined filesystems
        $this->setFilesystemMap($config, $container, $adapters);

        $container->setParameter('coshi_media', $config);
    }

    public function getConfiguration(array $configs, ContainerBuilder $container)
    {
        $factories = $this->createAdapterFactories();
        
        return new Configuration($factories);
    }

    public function setFilesystemMap(array $config, ContainerBuilder $container, array $adapters)
    {
        $map = [];

        foreach ($config['filesystems'] as $name => $filesystem) {
            $map[$name] = $this->createFilesystem($name, $filesystem, $container, $adapters);
        }
        
        $filesystemDefault = array_key_exists('filesystem_default', $config) ? $config['filesystem_default'] : null;

        $container->getDefinition('coshi_media.filesystem_map')
            ->replaceArgument(0, $map)
            ->replaceArgument(1, $filesystemDefault)
        ;
    }

    protected function getAdapters(ContainerBuilder $container, array $config, $factories)
    {
        $adapters = [];

        foreach ($config['adapters'] as $name => $adapter) {
            if (array_key_exists($name, $factories)) {
                $id = sprintf('coshi_media.gaufrette.adapter.%s', $name);

                $factories[$name]->create($container, $id, $adapter);
                $adapters[$name] = $id;

                continue;
            }

            throw new \LogicException(sprintf('The adapter \'%s\' is not configured.', $name));
        }

        return $adapters;
    }

    protected function createFilesystem($name, array $filesystemConfig, ContainerBuilder $container, array $adapters)
    {
        if (!array_key_exists($filesystemConfig['adapter'], $adapters)) {
            throw new \LogicException(sprintf('The adapter \'%s\' is not defined.', $filesystemConfig['adapter']));
        }

        $adapter = $adapters[$filesystemConfig['adapter']];
        $id = sprintf('coshi_media.%s_filesystem', $name);

        $container
            ->setDefinition($id, new DefinitionDecorator('coshi_media.filesystem'))
            ->replaceArgument(0, new Reference($adapter))
            ->replaceArgument(1, $name)
            ;


        return new Reference($id);
    }

    protected function createAdapterFactories()
    {
        if ($this->adaptersFactories !== null) {
            return $this->adaptersFactories;
        }

        $tempContainer = new ContainerBuilder();
        $loader        = new Loader\XmlFileLoader($tempContainer, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $services  = $tempContainer->findTaggedServiceIds('coshi_media.tag.gaufrette.adapter.factory');

        $factories = array();
        
        foreach (array_keys($services) as $id) {
            $factory = $tempContainer->get($id);
            $factories[str_replace('-', '_', $factory->getKey())] = $factory;
        }

        return $this->adaptersFactories = $factories;
    }


    public function setMediaClassOptions($config, $loadedConfig)
    {
        if (array_key_exists('media_class', $config)) {
            $loadedConfig['media_class'] = $config['media_class'];
        }
        return $loadedConfig;
    }
}
