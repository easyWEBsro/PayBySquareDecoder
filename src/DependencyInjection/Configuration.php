<?php

namespace Rikudou\BySquare\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('rikudou_pay_by_square_decoder');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('xz_path')
                    ->info('The path to the xz binary, null means auto detect')
                    ->defaultNull()
                ->end()
            ->end();
    }
}