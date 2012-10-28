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


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loadedConfig = $container->getParameter('coshi_media');

        $loadedConfig = $this->setImagerOptions($config, $loadedConfig);
        $loadedConfig = $this->setLinkMap($config, $loadedConfig);
        $config = $this->setUploaderOptions($config, $loadedConfig);

        $container->setParameter('coshi_media',$config);
    }

    public function setLinkMap(array $config, array $loadedConfig)
    {

        if(!array_key_exists('linkmap', $loadedConfig)) {
            $loadedConfig['linkmap']=array();
        }

        if(!array_key_exists('linkmap', $config)) {
            return $loadedConfig;
        }

        foreach ($config['linkmap'] as $class => $linkclass){
            $loadedConfig['linkmap'][$class] = $linkclass;
        }

        return $loadedConfig;
    }

    public function setUploaderOptions($config, $loadedConfig)
    {
        if(array_key_exists('uploader',$config))
        {
            foreach ($config['uploader'] as $k => $v){
                $loadedConfig['uploader'][$k]=$v;
            }

        }
        return $loadedConfig;
    }
    public function setImagerOptions($config, $loadedConfig)
    {
        if(array_key_exists('imager',$config))
        {
             $loadedConfig['imager']['options']['lib'] = (array_key_exists('lib', $config['imager']['options'])) ? $config['imager']['options']['lib'] : $loadedConfig['imager']['options']['lib'];
                 if(array_key_exists('thumbnails',$config['imager']['options']))
                 {
                    foreach ($config['imager']['options']['thumbnails'] as $k => $v) {
                        $loadedConfig['imager']['options']['thumbnails'][$k] = $v;
                    }
                 }
             return $loadedConfig;
        }
        return $loadedConfig;
    }


}
