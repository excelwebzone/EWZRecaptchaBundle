<?php

namespace EWZ\Bundle\RecaptchaBundle\Extension\ReCaptcha\RequestMethod;

use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod;
use ReCaptcha\RequestParameters;

/**
 * Sends POST requests to the reCAPTCHA service.
 */
class CurlPost implements RequestMethod
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
     * Curl connection to the reCAPTCHA service
     *
     * @var RequestMethod\Curl
     */
    private $curl;

    /**
     * Constructor.
     *
     * @param string   $recaptchaVerifyServer
     * @param int|null $timeout
     */
    public function __construct($recaptchaVerifyServer, $timeout)
    {
        $this->curl = new RequestMethod\Curl();
        $this->recaptchaVerifyUrl = ($recaptchaVerifyServer ?: 'https://www.google.com').'/recaptcha/api/siteverify';
        $this->timeout = $timeout;
        $this->cache = [];
    }

    /**
     * Submit the cURL request with the specified parameters.
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

        $handle = $this->curl->init($this->recaptchaVerifyUrl);

        $options = array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params->toQueryString(),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
            CURLINFO_HEADER_OUT => false,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => $this->timeout,
        );
        $this->curl->setoptArray($handle, $options);

        $response = $this->curl->exec($handle);
        $this->curl->close($handle);

        if ($response !== false) {
            return $response;
        }

        $result = '{"success": false, "error-codes": ["'.ReCaptcha::E_CONNECTION_FAILED.'"]}';
        $this->cache[$cacheKey] = $result;

        return $result;
    }
}
