<?php

namespace EWZ\Bundle\RecaptchaBundle\Bridge\Silex2\Form\Extension;

use Pimple\Container;
use Symfony\Component\Form\AbstractExtension;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

/**
 * Extends form to register captcha type
 */
class RecaptchaExtension extends AbstractExtension
{
    /**
     * Container
     *
     * @var Container
     */
    private $container;

    /**
     * Constructor
     *
     * @param Container $container container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Register the captcha form type
     *
     * @return array
     */
    protected function loadTypes()
    {
        return array(
            new EWZRecaptchaType(
                $this->container['ewz_recaptcha.public_key'],
                $this->container['ewz_recaptcha.enabled'],
                $this->container['ewz_recaptcha.ajax'],
                $this->container['ewz_recaptcha.locale.resolver']
            )
        );
    }
}
