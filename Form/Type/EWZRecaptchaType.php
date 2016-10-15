<?php

namespace EWZ\Bundle\RecaptchaBundle\Form\Type;

use EWZ\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * A field for entering a recaptcha text.
 */
class EWZRecaptchaType extends AbstractType
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
     * @var LocaleResolver
     */
    protected $localeResolver;

    /**
	 * @var Session
	 */
    protected $session;

    /**
	 * @var int
	 */
    protected $rememberMaxCount;

    /**
     * Construct.
     *
     * @param string  $publicKey Recaptcha public key
     * @param Boolean $enabled   Recaptache status
     * @param Boolean $ajax      Ajax status
     * @param string  $localeResolver
     * @param Session  $session
     * @param int  $rememberMaxCount
     */
    public function __construct($publicKey, $enabled, $ajax, $localeResolver, $session, $rememberMaxCount=0)
    {
        $this->publicKey = $publicKey;
        $this->enabled   = $enabled;
        $this->ajax      = $ajax;
        $this->localeResolver  = $localeResolver;
        $this->session  = $session;
        $this->rememberMaxCount  = $rememberMaxCount;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if(
            $this->rememberMaxCount
            && $this->session->get('reCaptcha.isNotARobot')
            && $this->session->get('reCaptcha.rememberTryCount') < $this->rememberMaxCount
        ){
            $this->enabled = false;
        }

        $view->vars = array_replace($view->vars, array(
            'ewz_recaptcha_enabled' => $this->enabled,
            'ewz_recaptcha_ajax'    => $this->ajax,
            'ewz_recaptcha_remember_max_count' => $this->rememberMaxCount,
            'ewz_recaptcha_remember_try_count' => $this->session->get('reCaptcha.rememberTryCount'),
        ));

        if (!$this->enabled) {
            return;
        }

        if (!isset($options['language'])) {
            $options['language'] = $this->localeResolver->resolve();
        }

        if (!$this->ajax) {
            $view->vars = array_replace($view->vars, array(
                'url_challenge' => sprintf('%s?hl=%s', self::RECAPTCHA_API_SERVER, $options['language']),
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
            'language'      => $this->localeResolver->resolve(),
            'public_key'    => null,
            'url_challenge' => null,
            'url_noscript'  => null,
            //'rememberMaxCount'  => 0, // TODO thinking of a proper way to store this for validation of this form only
            'attr'          => array(
                'options' => array(
                    'theme'           => 'light',
                    'type'            => 'image',
                    'size'            => 'normal',
                    'expiredCallback' => null,
                    'defer'           => false,
                    'async'           => false,
                )
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
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
