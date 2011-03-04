<?php

namespace EWZ\RecaptchaBundle\Form;

use Symfony\Component\Form\Field;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A field for entering a recaptcha text.
 */
class RecaptchaField extends Field
{
    /**
     * The reCAPTCHA server URL's
     */
    const RECAPTCHA_API_SERVER        = 'http://www.google.com/recaptcha/api';
    const RECAPTCHA_API_SECURE_SERVER = 'https://www.google.com/recaptcha/api';
    const RECAPTCHA_API_JS_SERVER     = 'http://www.google.com/recaptcha/api/js/recaptcha_ajax.js';

    /**
     * The javascript src attributes (challenge, noscript)
     *
     * @var string
     */
    protected $scripts;

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
     * Sets the Javascript source URLs.
     *
     * @param ContainerInterface $container An ContainerInterface instance
     */
    public function setScriptURLs($publicKey, $isSecure)
    {
        $this->pubkey = $publicKey;
        $this->secure = $isSecure;

        if ($this->pubkey == null || $this->pubkey == '') {
            throw new FormException('To use reCAPTCHA you must get an API key from <a href="https://www.google.com/recaptcha/admin/create">https://www.google.com/recaptcha/admin/create</a>');
        }


        if ($this->secure) {
            $server = self::RECAPTCHA_API_SECURE_SERVER;
        } else {
            $server = self::RECAPTCHA_API_SERVER;
        }

        $this->scripts = array(
            'challenge' => $server.'/challenge?k='.$this->pubkey,
            'noscript'  => $server.'/noscript?k='.$this->pubkey,
        );
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
