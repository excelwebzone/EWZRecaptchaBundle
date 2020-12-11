<?php

namespace EWZ\Bundle\RecaptchaBundle\Validator\Constraints;

use Psr\Log\LoggerInterface;
use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IsTrueValidatorV3 extends ConstraintValidator
{
    /** @var bool */
    private $enabled;

    /** @var string */
    private $secretKey;

    /** @var float */
    private $scoreThreshold;

    /** @var RequestStack */
    private $requestStack;

    /** @var LoggerInterface */
    private $logger;

    /**
     * ContainsRecaptchaValidator constructor.
     *
     * @param bool            $enabled
     * @param string          $secretKey
     * @param float           $scoreThreshold
     * @param RequestStack    $requestStack
     * @param LoggerInterface $logger
     */
    public function __construct(
        bool $enabled,
        string $secretKey,
        float $scoreThreshold,
        RequestStack $requestStack,
        LoggerInterface $logger
    ) {
        $this->enabled = $enabled;
        $this->secretKey = $secretKey;
        $this->scoreThreshold = $scoreThreshold;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$this->enabled) {
            return;
        }

        if (!$constraint instanceof IsTrueV3) {
            throw new UnexpectedTypeException($constraint, IsTrueV3::class);
        }

        if (null === $value) {
            $value = '';
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$this->isTokenValid($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    private function isTokenValid($token)
    {
        try {
            $remoteIp = $this->requestStack->getCurrentRequest()->getClientIp();

            $recaptcha = new ReCaptcha($this->secretKey);

            $response = $recaptcha
                ->setExpectedAction('form')
                ->setScoreThreshold($this->scoreThreshold)
                ->verify($token, $remoteIp);

            return $response->isSuccess();
        } catch (\Exception $exception) {
            $this->logger->error(
                'reCAPTCHA validator error: '.$exception->getMessage(),
                [
                    'exception' => $exception,
                ]
            );

            return false;
        }
    }
}
