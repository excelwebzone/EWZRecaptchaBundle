<?php

namespace EWZ\RecaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * configuration structure.
 */
class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\Config\Definition\NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ewz_recaptcha', 'array');

        $rootNode
            ->scalarNode('pubkey')->end()
            ->scalarNode('privkey')->end()
            ->booleanNode('secure')->end()
        ;

        return $treeBuilder->buildTree();
    }
}

