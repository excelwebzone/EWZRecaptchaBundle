<?php

namespace EWZ\Bundle\RecaptchaBundle\Validator\Constraints;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;

class TrueValidator extends ConstraintValidator
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
     * The reCAPTCHA server URL's
     */
    const RECAPTCHA_VERIFY_SERVER = 'https://www.google.com';

    /**
     * @param $enabled
     * @param $privateKey
     * @param RequestStack $requestStack
     * @param Session $session session service
     * @param boolean $remember
     * @param int $maxValidationTryCount
     */
    public function __construct($enabled, $privateKey, RequestStack $requestStack, Session $session, $remember, $maxValidationTryCount)
    {
        $this->enabled = $enabled;
        $this->privateKey = $privateKey;
        $this->requestStack = $requestStack;
        $this->session = $session;
        $this->remember = $remember;
        $this->maxValidationTryCount = $maxValidationTryCount;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $validationTryCount = $this->session->get('reCaptcha.validationTryCount');
        if(!is_integer($validationTryCount)){
            $validationTryCount = 0;
        }
        $validationTryCount++;
        $this->session->set('reCaptcha.validationTryCount', $validationTryCount);

        // if recaptcha is disabled, always valid
        if (!$this->enabled || ($this->remember && $this->session->get('reCaptcha.isNotARobot') && $validationTryCount <= $this->maxValidationTryCount) ) {
            return true;
        }

        // define variable for recaptcha check answer
        $remoteip = $this->requestStack->getMasterRequest()->server->get('REMOTE_ADDR');
        $response = $this->requestStack->getMasterRequest()->get('g-recaptcha-response');

        $isValid = $this->checkAnswer($this->privateKey, $remoteip, $response);

        if ($isValid) {
            $this->session->set('reCaptcha.isNotARobot', true);
            $this->session->set('reCaptcha.validationTryCount', 0);
        }
        else{
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

        return file_get_contents($host);
    }
}
