<?php

namespace EWZ\Bundle\RecaptchaBundle\Bridge\Silex2;

use EWZ\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueValidator;

/**
 * Silex Service Provider
 * Inject recaptcha configuration in pimple
 */
class RecaptchaServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        // parameters
        $container['ewz_recaptcha.public_key']          = null;
        $container['ewz_recaptcha.private_key']         = null;
        $container['ewz_recaptcha.locale_key']          = $container['locale'];
        $container['ewz_recaptcha.locale_from_request'] = false;
        $container['ewz_recaptcha.enabled']             = true;
        $container['ewz_recaptcha.ajax']                = false;
        $container['ewz_recaptcha.http_proxy']          = array(
            'host' => null,
            'port' => null,
            'auth' => null,
        );

        // register locale resolver
        $container['ewz_recaptcha.locale.resolver'] = function ($container) {
            return new LocaleResolver(
                $container['locale'],
                $container['ewz_recaptcha.locale_from_request'],
                $container['request_stack']
            );
        };

        // add loader for EWZ Template
        if (isset($container['twig'])) {
            $container->extend('twig.loader.filesystem', function ($loader, $container) {
                /** @var \Twig_Loader_Filesystem $loader */
                $path = dirname(__FILE__).'/../../Resources/views/Form';
                $loader->addPath($path);

                return $loader;
            });

            $container['twig.form.templates'] = array_merge(
                $container['twig.form.templates'],
                array('ewz_recaptcha_widget.html.twig')
            );
        }

        // register recaptcha form type
        if (isset($container['form.extensions'])) {
            $container->extend('form.extensions', function ($extensions, $container) {
                $extensions[] = new Form\Extension\RecaptchaExtension($container);

                return $extensions;
            });
        }

        // register recaptcha validator constraint
        if (isset($container['validator.validator_factory'])) {
            $container['ewz_recaptcha.true'] = function ($container) {
                $validator = new IsTrueValidator(
                    $container['ewz_recaptcha.enabled'],
                    $container['ewz_recaptcha.private_key'],
                    $container['request_stack'],
                    $container['ewz_recaptcha.http_proxy']
                );

                return $validator;
            };

            $container['validator.validator_service_ids'] = array_merge(
                $container['validator.validator_service_ids'],
                array('ewz_recaptcha.true' => 'ewz_recaptcha.true')
            );
        }

        // register translation files
        if (isset($container['translator'])) {
            $container->extend('translator', function ($translator, $container) {
                /** \Symfony\Component\Translation\Translator $translator */
                $translator->addResource(
                    'xliff',
                    dirname(__FILE__).'/../../Resources/translations/validators.'.$container['ewz_recaptcha.locale_key'].'.xlf',
                    $container['ewz_recaptcha.locale_key'],
                    'validators'
                );

                return $translator;
            });
        }
    }
}
