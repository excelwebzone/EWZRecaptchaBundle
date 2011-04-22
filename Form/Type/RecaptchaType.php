<?php

namespace EWZ\Bundle\RecaptchaBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Type\AbstractType;
use Symfony\Component\Form\Exception\FormException;

/**
 * A field for entering a recaptcha text.
 */
class RecaptchaType extends AbstractType
{
    /**
     * The reCAPTCHA server URL's
     */
    const RECAPTCHA_API_SERVER        = 'http://www.google.com/recaptcha/api';
    const RECAPTCHA_API_SECURE_SERVER = 'https://www.google.com/recaptcha/api';
    const RECAPTCHA_API_JS_SERVER     = 'http://www.google.com/recaptcha/api/js/recaptcha_ajax.js';

    /**
     * The public key
     *
     * @var string
     */
    protected $pubkey;

    /**
     * The security token
     *
     * @var string
     */
    protected $secure;

    /**
     * Construct.
     *
     * @param ContainerInterface $container An ContainerInterface instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->pubkey = $container->getParameter('recaptcha.pubkey');
        $this->secure = $container->getParameter('recaptcha.secure');

        if ($this->pubkey == null || $this->pubkey == '') {
            throw new FormException('To use reCAPTCHA you must get an API key from <a href="https://www.google.com/recaptcha/admin/create">https://www.google.com/recaptcha/admin/create</a>');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        if ($this->secure) {
            $server = self::RECAPTCHA_API_SECURE_SERVER;
        } else {
            $server = self::RECAPTCHA_API_SERVER;
        }

        $view->set('url_challenge', $server.'/challenge?k='.$this->pubkey);
        $view->set('url_noscript', $server.'/noscript?k='.$this->pubkey);

        $view->set('pubkey', $this->pubkey);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'pubkey'        => null,
            'url_challenge' => null,
            'url_noscript'  => null,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'field';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'recaptcha';
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
        return $this->pubkey;
    }
}
