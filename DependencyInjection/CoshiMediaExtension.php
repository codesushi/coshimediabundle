<?php

namespace Coshi\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CoshiMediaExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.xml');
        $loadedConfig = array();

        $loadedConfig = $this->setMediaClassOptions($config, $loadedConfig);
        $config = $this->setUploaderOptions($config, $loadedConfig);

        $container->setParameter('coshi_media', $config);
    }


    public function setUploaderOptions($config, $loadedConfig)
    {
        if (array_key_exists('uploader', $config)) {
            foreach ($config['uploader'] as $k => $v) {
                $loadedConfig['uploader'][$k] = $v;
            }

        }
        return $loadedConfig;
    }

    public function setMediaClassOptions($config, $loadedConfig)
    {
        if (array_key_exists('media_class', $config)) {
            $loadedConfig['media_class'] = $config['media_class'];
        }
        return $loadedConfig;
    }


}
