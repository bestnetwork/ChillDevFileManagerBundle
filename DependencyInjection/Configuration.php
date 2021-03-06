<?php

/**
 * This file is part of the ChillDev FileManager bundle.
 *
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @copyright 2012 - 2013 © by Rafał Wrzeszcz - Wrzasq.pl.
 * @version 0.1.2
 * @since 0.0.1
 * @package ChillDev\Bundle\FileManagerBundle
 */

namespace ChillDev\Bundle\FileManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * ChillDev.FileManager configuration handler.
 *
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @copyright 2012 - 2013 © by Rafał Wrzeszcz - Wrzasq.pl.
 * @version 0.1.2
 * @since 0.0.1
 * @package ChillDev\Bundle\FileManagerBundle
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     * @version 0.1.2
     * @since 0.0.1
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('chilldev_filemanager');

        // define parameters
        $rootNode
            ->children()
                ->booleanNode('sonata_block')
                    ->defaultFalse()
                    ->info('Sonata Admin block flag')
                ->end()
            ->end();
        $this->addDisksSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds disks configuration section.
     *
     * @param ArrayNodeDefinition $rootNode Root configuration node.
     * @return self Self instance.
     * @version 0.1.2
     * @since 0.1.2
     */
    protected function addDisksSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->fixXmlConfig('disk')
            ->children()
                ->arrayNode('disks')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('label')
                                ->isRequired()
                            ->end()
                            ->scalarNode('source')
                                ->isRequired()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $this;
    }
}
