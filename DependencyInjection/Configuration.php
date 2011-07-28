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
                ->scalarNode('public_key')->isRequired()->end()
                ->scalarNode('private_key')->isRequired()->end()
                ->booleanNode('secure')->defaultFalse()->end()
            ->end()
        ;

        return $tree->buildTree();
    }
}
