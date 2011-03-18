<?php

namespace EWZ\Bundle\RecaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * configuration structure.
 */
class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\Config\Definition\ArrayNode The config tree
     */
    public function getConfigTree()
    {
        $tree = new TreeBuilder();

        $tree->root('ewz_recaptcha')
            ->children()
                ->scalarNode('pubkey')->end()
                ->scalarNode('privkey')->end()
                ->booleanNode('secure')->defaultValue(false)->end()
            ->end()
        ;

        return $tree->buildTree();
    }
}
