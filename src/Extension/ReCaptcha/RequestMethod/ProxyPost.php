<?php

namespace EWZ\Bundle\RecaptchaBundle\Extension\ReCaptcha\RequestMethod;

use ReCaptcha\RequestMethod;
use ReCaptcha\RequestParameters;

/**
 * Sends POST requests to the reCAPTCHA service though a proxy.
 */
class ProxyPost implements RequestMethod
{
    /**
     * HTTP Proxy informations.
     *
     * @var array
     */
    private $httpProxy;

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

    /** @var array */
    private $cache;

    /**
     * Constructor.
     *
     * @param array    $httpProxy
     * @param string   $recaptchaVerifyServer
     * @param int|null $timeout
     */
    public function __construct(array $httpProxy, $recaptchaVerifyServer, $timeout)
    {
        $this->httpProxy = $httpProxy;
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
        $peerKey = version_compare(PHP_VERSION, '5.6.0', '<') ? 'CN_name' : 'peer_name';
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n".sprintf('Proxy-Authorization: Basic %s', base64_encode($this->httpProxy['auth'])),
                'method' => 'POST',
                'content' => $params->toQueryString(),
                // Force the peer to validate (not needed in 5.6.0+, but still works)
                'verify_peer' => true,
                // Force the peer validation to use www.google.com
                $peerKey => 'www.google.com',

                'proxy' => sprintf('tcp://%s:%s', $this->httpProxy['host'], $this->httpProxy['port']),
                // While this is a non-standard request format, some proxy servers require it.
                'request_fulluri' => true,
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
