<?php

namespace EWZ\Bundle\RecaptchaBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/**
 * EWZRecaptchaExtension.
 */
class EWZRecaptchaExtension extends Extension
{
    /**
     * Loads the recaptcha configuration.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('recaptcha.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ewz_recaptcha.public_key', $config['public_key']);
        $container->setParameter('ewz_recaptcha.private_key', $config['private_key']);
        $container->setParameter('ewz_recaptcha.secure', $config['secure']);
        $container->setParameter('ewz_recaptcha.enabled', $config['enabled']);
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
        return 'http://excelwebzone.com/schema/dic/ewz/recaptcha';
    }
}
