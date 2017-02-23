<?php

namespace EWZ\Bundle\RecaptchaBundle\Bridge\Form\Extension;

use Silex\Application;
use Symfony\Component\Form\AbstractExtension;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

/**
 * Extends form to register captcha type.
 */
class RecaptchaExtension extends AbstractExtension
{
    /**
     * Container.
     *
     * @var \Silex\Application
     */
    private $app;

    /**
     * @param \Silex\Application $app container
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register the captche form type.
     *
     * @return array
     */
    protected function loadTypes()
    {
        return array(
            new EWZRecaptchaType(
                $this->app['ewz_recaptcha.public_key'],
                $this->app['ewz_recaptcha.enabled'],
                $this->app['ewz_recaptcha.ajax'],
                $this->app['ewz_recaptcha.locale_key']
            ),
        );
    }
}
