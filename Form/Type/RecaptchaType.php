<?php

namespace EWZ\Bundle\RecaptchaBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A field for entering a recaptcha text.
 */
class RecaptchaType extends AbstractType
{
    /**
     * The reCAPTCHA server URL's
     */
    const RECAPTCHA_API_SERVER    = 'https://www.google.com/recaptcha/api.js';
    const RECAPTCHA_API_JS_SERVER = '//www.google.com/recaptcha/api/js/recaptcha_ajax.js';

    /**
     * The public key
     *
     * @var string
     */
    protected $publicKey;

    /**
     * Enable recaptcha?
     *
     * @var Boolean
     */
    protected $enabled;

    /**
     * Use AJAX api?
     *
     * @var Boolean
     */
    protected $ajax;

    /**
     * Language
     *
     * @var string
     */
    protected $language;

    /**
     * Construct.
     *
     * @param string  $publicKey Recaptcha public key
     * @param Boolean $enabled Recaptache status
     * @param string  $language language or locale code
     */
    public function __construct($publicKey, $enabled, $ajax, $language)
    {
        $this->publicKey = $publicKey;
        $this->enabled   = $enabled;
        $this->ajax      = $ajax;
        $this->language  = $language;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'ewz_recaptcha_enabled' => $this->enabled,
            'ewz_recaptcha_ajax'    => $this->ajax,
        ));

        if (!$this->enabled) {
            return;
        }

        if (!$this->ajax) {
            $view->vars = array_replace($view->vars, array(
                'url_challenge' => sprintf('%s?hl=%s', self::RECAPTCHA_API_SERVER, $this->language),
                'public_key'    => $this->publicKey,
            ));
        } else {
            $view->vars = array_replace($view->vars, array(
                'url_api'    => self::RECAPTCHA_API_JS_SERVER,
                'public_key' => $this->publicKey,
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'compound'      => false,
            'public_key'    => null,
            'url_challenge' => null,
            'url_noscript'  => null,
            'attr'          => array(
                'options' => array(
                    'theme' => 'light',
                    'type'  => 'image'
                )
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ewz_recaptcha';
    }

    /**
     * Gets the Javascript source URLs.
     *
     * @param string $key The script name
     *
     * @return string The javascript source URL
     */
    public function getScriptURL($key)
    {
        return isset($this->scripts[$key]) ? $this->scripts[$key] : null;
    }

    /**
     * Gets the public key.
     *
     * @return string The javascript source URL
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }
}
