<?php

namespace Bundle\RecaptchaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RecaptchaExtension extends Extension
{
    public function configLoad(array $configs, ContainerBuilder $container)
    {
        foreach ($configs as $config) {
            $this->doConfigLoad($config, $container);
        }
    }

    /**
     * Loads the recaptcha configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    protected function doConfigLoad(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (!$container->hasDefinition('recaptcha')) {
            $loader->load('recaptcha.xml');
        }

        if (isset($config['pubkey'])) {
            $container->setParameter('recaptcha.pubkey', $config['pubkey']);
        }

        if (isset($config['privkey'])) {
            $container->setParameter('recaptcha.privkey', $config['privkey']);
        }

        if (isset($config['secure'])) {
            $container->setParameter('recaptcha.secure', $config['secure']);
        }
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     */
    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/recaptcha';
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public function getAlias()
    {
        return 'recaptcha';
    }
}
