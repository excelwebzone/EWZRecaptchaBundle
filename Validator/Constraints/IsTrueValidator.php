<?php

namespace EWZ\Bundle\RecaptchaBundle\Validator\Constraints;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\HttpFoundation\Session\Session;

class IsTrueValidator extends ConstraintValidator
{
    /**
     * Enable recaptcha?
     *
     * @var Boolean
     */
    protected $enabled;

    /**
     * Recaptcha Private Key
     *
     * @var Boolean
     */
    protected $privateKey;

    /**
     * Request Stack
     *
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * HTTP Proxy informations
     * @var Array
     */
    protected $httpProxy;

    /**
	 * @var Session
	 */
    protected $session;

    /**
	 * @var int
	 */
    protected $rememberMaxCount;

    /**
     * The reCAPTCHA server URL's
     */
    const RECAPTCHA_VERIFY_SERVER = 'https://www.google.com';

    /**
     * Construct.
     *
     * @param Boolean      $enabled
     * @param string       $privateKey
     * @param RequestStack $requestStack
     * @param array        $httpProxy
     * @param Session      $session
     * @param int          $rememberMaxCount
     */
    public function __construct($enabled, $privateKey, RequestStack $requestStack, array $httpProxy, $session, $rememberMaxCount=0)
    {
        $this->enabled      = $enabled;
        $this->privateKey   = $privateKey;
        $this->requestStack = $requestStack;
        $this->httpProxy    = $httpProxy;
        $this->session  = $session;
        $this->rememberMaxCount  = $rememberMaxCount;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $rememberTryCount = $this->session->get('reCaptcha.rememberTryCount');
        if(!is_integer($rememberTryCount)){
            $rememberTryCount = 0;
        }
        $rememberTryCount++;
        $this->session->set('reCaptcha.rememberTryCount', $rememberTryCount);

        // if recaptcha is disabled, always valid
        if (!$this->enabled || ($this->rememberMaxCount && $this->session->get('reCaptcha.isNotARobot') && $rememberTryCount <= $this->rememberMaxCount) ) {
            return;
        }

        // define variable for recaptcha check answer
        $remoteip = $this->requestStack->getMasterRequest()->getClientIp();
        $response = $this->requestStack->getMasterRequest()->get('g-recaptcha-response');

        $isValid = $this->checkAnswer($this->privateKey, $remoteip, $response);

        if ($isValid) {
            $this->session->set('reCaptcha.isNotARobot', true);
            $this->session->set('reCaptcha.rememberTryCount', 0);
        }
        else {
        	$this->session->set('reCaptcha.isNotARobot', false);
            $this->context->addViolation($constraint->message);
        }
    }

    /**
      * Calls an HTTP POST function to verify if the user's guess was correct.
      *
      * @param string $privateKey
      * @param string $remoteip
      * @param string $response
      *
      * @throws ValidatorException When missing remote ip
      *
      * @return Boolean
      */
    private function checkAnswer($privateKey, $remoteip, $response)
    {
        if ($remoteip == null || $remoteip == '') {
            throw new ValidatorException('For security reasons, you must pass the remote ip to reCAPTCHA');
        }

        // discard spam submissions
        if ($response == null || strlen($response) == 0) {
            return false;
        }

        $response = $this->httpGet(self::RECAPTCHA_VERIFY_SERVER, '/recaptcha/api/siteverify', array(
            'secret'   => $privateKey,
            'remoteip' => $remoteip,
            'response' => $response
        ));

        $response = json_decode($response, true);

        if ($response['success'] == true) {
            return true;
        }

        return false;
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
