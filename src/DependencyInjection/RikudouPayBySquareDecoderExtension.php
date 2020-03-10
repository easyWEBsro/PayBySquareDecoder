<?php

namespace Rikudou\BySquare\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class RikudouPayBySquareDecoderExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.xml');

        $configs = $this->processConfiguration(new Configuration(), $configs);
        $definition = $container->getDefinition('rikudou.by_square.decoder');
        $definition->addMethodCall('setXzBinary', [$configs['xz_path']]);
    }
}
