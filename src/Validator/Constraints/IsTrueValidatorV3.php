<?php

namespace EWZ\Bundle\RecaptchaBundle\Validator\Constraints;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaV3Type;
use Psr\Log\LoggerInterface;
use ReCaptcha\ReCaptcha;
use Symfony\Component\Form\FormInterface;
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
    
    /** @var ReCaptcha */
    private $reCaptcha;

    /** @var RequestStack */
    private $requestStack;

    /** @var LoggerInterface */
    private $logger;

    /**
     * ContainsRecaptchaValidator constructor.
     *
     * @param bool            $enabled
     * @param float           $scoreThreshold
     * @param ReCaptcha       $scoreThreshold
     * @param RequestStack    $requestStack
     * @param LoggerInterface $logger
     */
    public function __construct(
        bool $enabled,
        float $scoreThreshold,
        ReCaptcha $reCaptcha,
        RequestStack $requestStack,
        LoggerInterface $logger
    ) {
        $this->enabled = $enabled;
        $this->scoreThreshold = $scoreThreshold;
        $this->reCaptcha = $reCaptcha;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
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
    private function isTokenValid(string $token): bool
    {
        try {
            $remoteIp = $this->requestStack->getCurrentRequest()->getClientIp();
            $action = $this->getActionName();

            $response = $this->reCaptcha
                ->setExpectedAction($action)
                ->setScoreThreshold($this->scoreThreshold)
                ->verify($token, $remoteIp);

            return $response->isSuccess();
        } catch (\Throwable $exception) {
            $this->logger->error(
                'reCAPTCHA validator error: '.$exception->getMessage(),
                [
                    'exception' => $exception,
                ]
            );

            return false;
        }
    }

    private function getActionName(): string
    {
        $object = $this->context->getObject();
        $action = null;

        if ($object instanceof FormInterface) {
            $action = $object->getConfig()->getOption('action_name');
        }

        return $action ?: EWZRecaptchaV3Type::DEFAULT_ACTION_NAME;
    }
}
