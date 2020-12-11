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

                ->integerNode('version')->min(2)->max(3)->defaultValue(2)->end()
                ->booleanNode('hide_badge')->defaultValue(false)->end()
                ->floatNode('score_threshold')->min(0.0)->max(1.0)->defaultValue(0.5)->end()

                ->integerNode('timeout')->min(0)->defaultNull()->end()
                ->arrayNode('trusted_roles')->prototype('scalar')->treatNullLike(array())->end()
            ->end()
        ;

        $this->addHttpClientConfiguration($rootNode);
        $this->addServiceDefinitionConfiguration($rootNode);

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

    private function addServiceDefinitionConfiguration(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('service_definition')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('service_name')->isRequired()->end()
                            ->arrayNode('options')
                                ->children()
                                    ->scalarNode('action_name')->end()
                                    ->scalarNode('script_nonce_csp')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
