<?php

namespace EWZ\Bundle\RecaptchaBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Session\Session;

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
     * @var Session
     */
    protected $session;

    /**
     * @var boolean
     */
    protected $remember = false;

	/**
	 * @var int
	 */
	protected $maxValidationTryCount = 5;

    /**
     * Construct.
     *
     * @param string  $publicKey Recaptcha public key
     * @param Boolean $enabled Recaptache status
     * @param Boolean $ajax ajax mode
     * @param string  $language language or locale code
     * @param \Symfony\Component\HttpFoundation\Session\Session  $session session service
     * @param Boolean $remember remember mode
     * @param int $maxValidationTryCount the maximum number of times a user can pass validation without having to revalidate the captcha field
     */
    public function __construct($publicKey, $enabled, $ajax, $language, $session, $remember, $maxValidationTryCount)
    {
        $this->publicKey = $publicKey;
        $this->enabled   = $enabled;
        $this->ajax      = $ajax;
        $this->language  = $language;
        $this->session = $session;
        $this->remember = $remember;
        $this->maxValidationTryCount = $maxValidationTryCount;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /*if(array_key_exists('remember', $options)){
            $this->remember = (bool)((int)$options['remember']); // TODO thinking of a proper way to store this for validation of this form only
        }
        if(array_key_exists('max_validation_try_count', $options)){
            $this->maxVvalidationTryCount = (int)(''.$options['max_validation_try_count']); // TODO thinking of a proper way to store this for validation of this form only
        }*/

        if($this->remember && $this->session->get('reCaptcha.isNotARobot') && $this->session->get('reCaptcha.validationTryCount') < $this->maxValidationTryCount){
            $this->enabled = false;
        }

        $view->vars = array_replace($view->vars, array(
            'ewz_recaptcha_try' => $this->session->get('reCaptcha.validationTryCount'),
            'ewz_recaptcha_max_try' => $this->maxValidationTryCount,
            'ewz_recaptcha_remember' => $this->remember,
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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'compound'      => false,
            //'remember'      => false, // TODO thinking of a proper way to store this for validation of this form only
            //'max_validation_try_count' => 5, // TODO thinking of a proper way to store this for validation of this form only
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
