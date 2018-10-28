<?php

namespace EWZ\Bundle\RecaptchaBundle\Form\Type;

use EWZ\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A field for entering a recaptcha text.
 */
class EWZRecaptchaType extends AbstractType
{
    /**
     * The reCAPTCHA server URL.
     * 
     * @var string
     */
    protected $recaptchaApiServer;
    
    /**
     * The reCAPTCHA JS server URL.
     * 
     * @var string
     */
    protected $recaptchaApiJsServer;

    /**
     * The public key.
     *
     * @var string
     */
    protected $publicKey;

    /**
     * The API server host name.
     *
     * @var string
     */
    protected $apiHost;

    /**
     * Enable recaptcha?
     *
     * @var bool
     */
    protected $enabled;

    /**
     * Use AJAX api?
     *
     * @var bool
     */
    protected $ajax;

    /**
     * @var LocaleResolver
     */
    protected $localeResolver;

    /**
     * @param string         $publicKey      Recaptcha public key
     * @param bool           $enabled        Recaptcha status
     * @param bool           $ajax           Ajax status
     * @param LocaleResolver $localeResolver
     */
    public function __construct($publicKey, $enabled, $ajax, LocaleResolver $localeResolver, $apiHost = 'www.google.com')
    {
        $this->publicKey = $publicKey;
        $this->enabled = $enabled;
        $this->ajax = $ajax;
        $this->apiHost = $apiHost;
        $this->localeResolver = $localeResolver;
        $this->recaptchaApiJsServer = sprintf('//%s/recaptcha/api/js/recaptcha_ajax.js', $apiHost);
        $this->recaptchaApiServer = sprintf('https://%s/recaptcha/api.js', $apiHost);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'ewz_recaptcha_enabled' => $this->enabled,
            'ewz_recaptcha_ajax' => $this->ajax,
            'ewz_recaptcha_apihost' => $this->apiHost
        ));

        if (!$this->enabled) {
            return;
        }

        if (!isset($options['language'])) {
            $options['language'] = $this->localeResolver->resolve();
        }

        if (!$this->ajax) {
            $view->vars = array_replace($view->vars, array(
                'url_challenge' => sprintf('%s?hl=%s', $this->recaptchaApiServer, $options['language']),
                'public_key' => $this->publicKey,
            ));
        } else {
            $view->vars = array_replace($view->vars, array(
                'url_api' => $this->recaptchaApiJsServer,
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
            'compound' => false,
            'language' => $this->localeResolver->resolve(),
            'public_key' => null,
            'url_challenge' => null,
            'url_noscript' => null,
            'attr' => array(
                'options' => array(
                    'theme' => 'light',
                    'type' => 'image',
                    'size' => 'normal',
                    'callback' => null,
                    'expiredCallback' => null,
                    'bind' => null,
                    'defer' => false,
                    'async' => false,
                    'badge' => null,
                ),
            ),
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

    /**
     * Gets the API host name.
     *
     * @return string The hostname for API
     */
    public function getApiHost()
    {
        return $this->apiHost;
    }
}
