<?php

namespace EWZ\Bundle\RecaptchaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 */
class EWZRecaptchaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        foreach ($config as $key => $value) {
            $container->setParameter('ewz_recaptcha.'.$key, $value);
        }

        $this->registerWidget($container);
    }

    /**
     * Registers the form widget.
     *
     * @param ContainerBuilder $container
     */
    protected function registerWidget(ContainerBuilder $container)
    {
        $templatingEngines = $container->hasParameter('templating.engines')
            ? $container->getParameter('templating.engines')
            : array('twig');

        if (in_array('php', $templatingEngines)) {
            $formResource = 'EWZRecaptchaBundle:Form';

            $container->setParameter('templating.helper.form.resources', array_merge(
                $container->getParameter('templating.helper.form.resources'),
                array($formResource)
            ));
        }

        if (in_array('twig', $templatingEngines)) {
            $formResource = 'EWZRecaptchaBundle:Form:ewz_recaptcha_widget.html.twig';

            $container->setParameter('twig.form.resources', array_merge(
                $this->getTwigFormResources($container),
                array($formResource)
            ));
        }
    }

    private function getTwigFormResources(ContainerBuilder $container)
    {
        if (!$container->hasParameter('twig.form.resources'))
            return array();

        return $container->getParameter('twig.form.resources');
    }
}
