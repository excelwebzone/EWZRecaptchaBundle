<?php

namespace EWZ\Bundle\RecaptchaBundle\Extension\ReCaptcha\RequestMethod;

use ReCaptcha\RequestMethod;
use ReCaptcha\RequestParameters;

/**
 * Sends POST requests to the reCAPTCHA service.
 */
class Post implements RequestMethod
{
    /**
     * The reCAPTCHA verify server URL.
     *
     * @var string
     */
    private $recaptchaVerifyUrl;

    /**
     * The timeout for the reCAPTCHA verification.
     *
     * @var int|null
     */
    private $timeout;

    /**
     * @var array
     */
    private $cache;

    /**
     * Constructor.
     *
     * @param string   $recaptchaVerifyServer
     * @param int|null $timeout
     */
    public function __construct($recaptchaVerifyServer, $timeout)
    {
        $this->recaptchaVerifyUrl = ($recaptchaVerifyServer ?: 'https://www.google.com').'/recaptcha/api/siteverify';
        $this->timeout = $timeout;
        $this->cache = [];
    }

    /**
     * Submit the POST request with the specified parameters.
     *
     * @param RequestParameters $params Request parameters
     *
     * @return string Body of the reCAPTCHA response
     */
    public function submit(RequestParameters $params)
    {
        $cacheKey = $params->toQueryString();
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        /**
         * PHP 5.6.0 changed the way you specify the peer name for SSL context options.
         * Using "CN_name" will still work, but it will raise deprecated errors.
         */
        $peer_key = version_compare(PHP_VERSION, '5.6.0', '<') ? 'CN_name' : 'peer_name';
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => $params->toQueryString(),
                // Force the peer to validate (not needed in 5.6.0+, but still works)
                'verify_peer' => true,
                // Force the peer validation to use www.google.com
                $peer_key => 'www.google.com',
            ),
        );
        if (null !== $this->timeout) {
            $options['http']['timeout'] = $this->timeout;
        }
        $context = stream_context_create($options);
        $result = file_get_contents($this->recaptchaVerifyUrl, false, $context);

        $this->cache[$cacheKey] = $result;

        return $result;
    }
}
