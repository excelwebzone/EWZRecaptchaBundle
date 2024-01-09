<?php

namespace EWZ\Bundle\RecaptchaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class IsTrue extends Constraint
{
    public $message = 'This value is not a valid captcha.';

    public $invalidHostMessage = 'The captcha was not resolved on the right domain.';

    public function __construct(array $options = null, string $message = null, string $invalidHostMessage = null, array $groups = null, $payload = null)
    {
        parent::__construct($options ?? [], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->invalidHostMessage = $invalidHostMessage ?? $this->invalidHostMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'ewz_recaptcha.true';
    }
}
