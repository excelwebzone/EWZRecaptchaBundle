<?php

namespace EWZ\Bundle\RecaptchaBundle\Validator\Constraints;

use EWZ\Bundle\RecaptchaBundle\Extension\ReCaptcha\RequestMethod\Post;
use EWZ\Bundle\RecaptchaBundle\Extension\ReCaptcha\RequestMethod\ProxyPost;
use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

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
     * @var string
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
     * Authorization Checker
     *
     * @var AuthorizationChecker
     */
    protected $authorizationChecker;

    /**
     * Trusted Roles
     *
     * @var array
     */
    protected $trustedRoles;

    /**
     * The reCAPTCHA verify server URL.
     *
     * @var string
     */
    protected $recaptchaVerifyServer;

    /**
     * @param bool                               $enabled
     * @param string                             $privateKey
     * @param RequestStack                       $requestStack
     * @param array                              $httpProxy
     * @param bool                               $verifyHost
     * @param AuthorizationCheckerInterface|null $authorizationChecker
     * @param array                              $trustedRoles
     * @param string                             $apiHost
     */
    public function __construct(
        $enabled,
        $privateKey,
        RequestStack $requestStack,
        array $httpProxy,
        $verifyHost,
        AuthorizationCheckerInterface $authorizationChecker = null,
        array $trustedRoles = array(),
        $apiHost = 'www.google.com')
    {
        $this->enabled = $enabled;
        $this->privateKey = $privateKey;
        $this->requestStack = $requestStack;
        $this->httpProxy = $httpProxy;
        $this->verifyHost = $verifyHost;
        $this->authorizationChecker = $authorizationChecker;
        $this->trustedRoles = $trustedRoles;
        $this->recaptchaVerifyServer = 'https://'.$apiHost;
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

        // if we have an authorized role
        if ($this->authorizationChecker
            && count($this->trustedRoles) > 0
            && $this->authorizationChecker->isGranted($this->trustedRoles)) {
            return;
        }

        // define variable for recaptcha check answer
        $masterRequest = $this->requestStack->getMasterRequest();
        $remoteip = $masterRequest->getClientIp();
        $answer = $masterRequest->get('g-recaptcha-response');

        // Verify user response with Google
        if (null !== $this->httpProxy['host'] && null !== $this->httpProxy['port']) {
            $requestMethod = new ProxyPost($this->httpProxy, $this->recaptchaVerifyServer);
        } else {
            $requestMethod = new Post($this->recaptchaVerifyServer);
        }
        $recaptcha = new ReCaptcha($this->privateKey, $requestMethod);
        $response = $recaptcha->verify($answer, $remoteip);

        if (!$response->isSuccess()) {
            $this->context->addViolation($constraint->message);
        }
        // Perform server side hostname check
        elseif ($this->verifyHost && $response->getHostname() !== $masterRequest->getHost()) {
            $this->context->addViolation($constraint->invalidHostMessage);
        }
    }
}
