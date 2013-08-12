<?php

namespace Black\Bundle\CategoryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
        $rootNode = $treeBuilder->root('black_category');

        $supportedDrivers = array('mongodb', 'orm');

        $rootNode
            ->children()

                ->scalarNode('db_driver')
                    ->isRequired()
                    ->validate()
                        ->ifNotInArray($supportedDrivers)
                        ->thenInvalid('The database driver must be either \'mongodb\', \'orm\'.')
                    ->end()
                ->end()

                ->scalarNode('category_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('category_manager')->defaultValue('Black\\Bundle\\CategoryBundle\\Doctrine\\CategoryManager')->end()
            ->end();

        $this->addCategorySection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addCategorySection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('category')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                        ->children()
                        ->arrayNode('form')
                        ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('name')
                                    ->defaultValue('black_category_form')
                                ->end()
                                ->scalarNode('type')
                                    ->defaultValue('Black\\Bundle\\CategoryBundle\\Form\\Type\\CategoryType')
                                ->end()
                                ->scalarNode('handler')
                                    ->defaultValue('Black\\Bundle\\CategoryBundle\\Form\\Handler\\CategoryFormHandler')
                                ->end()
                            ->end()
                        ->end()

                    ->end()
                ->end()
            ->end();
    }
}
