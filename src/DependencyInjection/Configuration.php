<?php

namespace EWZ\Bundle\RecaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ewz_recaptcha');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('ewz_recaptcha');
        }

        $rootNode
            ->children()
                ->scalarNode('public_key')->isRequired()->end()
                ->scalarNode('private_key')->isRequired()->end()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->booleanNode('verify_host')->defaultFalse()->end()
                ->booleanNode('ajax')->defaultFalse()->end()
                ->scalarNode('locale_key')->defaultValue('%kernel.default_locale%')->end()
                ->scalarNode('api_host')->defaultValue('www.google.com')->end()
                ->booleanNode('locale_from_request')->defaultFalse()->end()
                ->integerNode('timeout')->min(0)->defaultNull()->end()
				->arrayNode('trusted_roles')->prototype('scalar')->treatNullLike(array())->end()
            ->end()
        ;

        $this->addHttpClientConfiguration($rootNode);

        return $treeBuilder;
    }

    private function addHttpClientConfiguration(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('http_proxy')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('host')->defaultValue(null)->end()
                        ->scalarNode('port')->defaultValue(null)->end()
                        ->scalarNode('auth')->defaultValue(null)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
