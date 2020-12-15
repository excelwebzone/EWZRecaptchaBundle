<?php

namespace EWZ\Bundle\RecaptchaBundle\DependencyInjection;

use EWZ\Bundle\RecaptchaBundle\Factory\EWZRecaptchaV2FormBuilderFactory;
use EWZ\Bundle\RecaptchaBundle\Factory\EWZRecaptchaV3FormBuilderFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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

        $this->registerWidget($container, $config['version']);

        if (null !== $config['http_proxy']['host'] && null !== $config['http_proxy']['port']) {
            $recaptchaService = $container->findDefinition('ewz_recaptcha.recaptcha');
            $recaptchaService->replaceArgument(1, new Reference('ewz_recaptcha.extension.recaptcha.request_method.proxy_post'));
        }

        if (3 == $config['version']) {
            $container->register('ewz_recaptcha.form_builder_factory', EWZRecaptchaV3FormBuilderFactory::class)
                ->addArgument(new Reference(FormFactoryInterface::class));
        } else {
            $container->register('ewz_recaptcha.form_builder_factory', EWZRecaptchaV2FormBuilderFactory::class)
                ->addArgument(new Reference(FormFactoryInterface::class));
        }

        foreach ($config['service_definition'] as $serviceDefinition) {
            $container->register('ewz_recaptcha.'.$serviceDefinition['service_name'], FormBuilderInterface::class)
                ->setFactory(array(
                    new Reference('ewz_recaptcha.form_builder_factory'),
                    'get',
                ))
                ->setArguments([$serviceDefinition['options']]);
        }
    }

    /**
     * Registers the form widget.
     *
     * @param ContainerBuilder $container
     */
    protected function registerWidget(ContainerBuilder $container, $version = 2)
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
            $formResource = '@EWZRecaptcha/Form/ewz_recaptcha_widget.html.twig';
            if (3 === $version) {
                $formResource = '@EWZRecaptcha/Form/v3/ewz_recaptcha_widget.html.twig';
            }

            $container->setParameter('twig.form.resources', array_merge(
                $this->getTwigFormResources($container),
                array($formResource)
            ));
        }
    }

    private function getTwigFormResources(ContainerBuilder $container)
    {
        if (!$container->hasParameter('twig.form.resources')) {
            return [];
        }

        return $container->getParameter('twig.form.resources');
    }
}
