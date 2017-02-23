<?php

namespace EWZ\Bundle\RecaptchaBundle\Bridge;

use Silex\Application;
use Silex\ServiceProviderInterface;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueValidator;

/**
 * Silex Service Provider
 * Inject recaptcha configuration in pimple.
 */
class RecaptchaServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        // parameters
        $app['ewz_recaptcha.public_key'] = null;
        $app['ewz_recaptcha.private_key'] = null;
        $app['ewz_recaptcha.locale_key'] = $app['locale'];
        $app['ewz_recaptcha.enabled'] = true;
        $app['ewz_recaptcha.verify_host'] = false;
        $app['ewz_recaptcha.ajax'] = false;
        $app['ewz_recaptcha.http_proxy'] = array(
            'host' => null,
            'port' => null,
            'auth' => null,
        );

        // add loader for EWZ Template
        if (isset($app['twig'])) {
            $path = dirname(__FILE__).'/../Resources/views/Form';
            $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($path));

            $app['twig.form.templates'] = array_merge(
                $app['twig.form.templates'],
                array('ewz_recaptcha_widget.html.twig')
            );
        }

        // register recaptcha form type
        if (isset($app['form.extensions'])) {
            $app['form.extensions'] = $app->share($app->extend('form.extensions',
                function ($extensions) use ($app) {
                    $extensions[] = new Form\Extension\RecaptchaExtension($app);

                    return $extensions;
                }));
        }

        // register recaptcha validator constraint
        if (isset($app['validator.validator_factory'])) {
            $app['ewz_recaptcha.true'] = $app->share(function ($app) {
                $validator = new IsTrueValidator(
                    $app['ewz_recaptcha.enabled'],
                    $app['ewz_recaptcha.private_key'],
                    $app['request_stack'],
                    $app['ewz_recaptcha.http_proxy'],
                    $app['ewz_recaptcha.verify_host']
                );

                return $validator;
            });

            $app['validator.validator_service_ids'] =
                    isset($app['validator.validator_service_ids']) ? $app['validator.validator_service_ids'] : array();
            $app['validator.validator_service_ids'] = array_merge(
                $app['validator.validator_service_ids'],
                array('ewz_recaptcha.true' => 'ewz_recaptcha.true')
            );
        }

        // register translation files
        if (isset($app['translator'])) {
            $app['translator'] = $app->share($app->extend('translator', function ($translator, $app) {
                $translator->addResource(
                    'xliff',
                    dirname(__FILE__).'/../Resources/translations/validators.'.$app['ewz_recaptcha.locale_key'].'.xlf',
                    $app['ewz_recaptcha.locale_key'],
                    'validators'
                );

                return $translator;
            }));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
