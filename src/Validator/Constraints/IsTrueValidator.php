<?php

namespace EWZ\Bundle\RecaptchaBundle\Validator\Constraints;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;

class IsTrueValidator extends ConstraintValidator
{
    /**
     * Enable recaptcha?
     *
     * @var bool
     */
    protected $enabled;

    /**
     * Recaptcha Private Key.
     *
     * @var bool
     */
    protected $privateKey;

    /**
     * Request Stack.
     *
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * HTTP Proxy informations.
     *
     * @var array
     */
    protected $httpProxy;

    /**
     * Enable serverside host check.
     *
     * @var bool
     */
    protected $verifyHost;

    /**
     * The reCAPTCHA server URL's.
     */
    const RECAPTCHA_VERIFY_SERVER = 'https://www.google.com';

    /**
     * @param bool         $enabled
     * @param string       $privateKey
     * @param RequestStack $requestStack
     * @param array        $httpProxy
     * @param bool         $verifyHost
     */
    public function __construct($enabled, $privateKey, RequestStack $requestStack, array $httpProxy, $verifyHost)
    {
        $this->enabled = $enabled;
        $this->privateKey = $privateKey;
        $this->requestStack = $requestStack;
        $this->httpProxy = $httpProxy;
        $this->verifyHost = $verifyHost;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        // if recaptcha is disabled, always valid
        if (!$this->enabled) {
            return;
        }

        // define variable for recaptcha check answer
        $masterRequest = $this->requestStack->getMasterRequest();
        $remoteip = $masterRequest->getClientIp();
        $answer = $masterRequest->get('g-recaptcha-response');

        // Verify user response with Google
        $response = $this->checkAnswer($this->privateKey, $remoteip, $answer);

        if ($response['success'] !== true) {
            $this->context->addViolation($constraint->message);
        }
        // Perform server side hostname check
        elseif ($this->verifyHost && $response['hostname'] !== $masterRequest->getHost()) {
            $this->context->addViolation($constraint->invalidHostMessage);
        }
    }

    /**
     * Calls an HTTP POST function to verify if the user's guess was correct.
     *
     * @param string $privateKey
     * @param string $remoteip
     * @param string $answer
     *
     * @throws ValidatorException When missing remote ip
     *
     * @return bool
     */
    private function checkAnswer($privateKey, $remoteip, $answer)
    {
        if ($remoteip == null || $remoteip == '') {
            throw new ValidatorException('For security reasons, you must pass the remote ip to reCAPTCHA');
        }

        // discard spam submissions
        if ($answer == null || strlen($answer) == 0) {
            return false;
        }

        $response = $this->httpGet(self::RECAPTCHA_VERIFY_SERVER, '/recaptcha/api/siteverify', array(
            'secret' => $privateKey,
            'remoteip' => $remoteip,
            'response' => $answer,
        ));

        return json_decode($response, true);
    }

    /**
     * Submits an HTTP POST to a reCAPTCHA server.
     *
     * @param string $host
     * @param string $path
     * @param array  $data
     *
     * @return array response
     */
    private function httpGet($host, $path, $data)
    {
        $host = sprintf('%s%s?%s', $host, $path, http_build_query($data));

        $context = $this->getResourceContext();

        return file_get_contents($host, false, $context);
    }

    /**
     * @return null|resource
     */
    private function getResourceContext()
    {
        if (null === $this->httpProxy['host'] || null === $this->httpProxy['port']) {
            return null;
        }

        $options = array();
        foreach (array('http', 'https') as $protocol) {
            $options[$protocol] = array(
                'method' => 'GET',
                'proxy' => sprintf('tcp://%s:%s', $this->httpProxy['host'], $this->httpProxy['port']),
                'request_fulluri' => true,
            );

            if (null !== $this->httpProxy['auth']) {
                $options[$protocol]['header'] = sprintf('Proxy-Authorization: Basic %s', base64_encode($this->httpProxy['auth']));
            }
        }

        return stream_context_create($options);
    }
}
