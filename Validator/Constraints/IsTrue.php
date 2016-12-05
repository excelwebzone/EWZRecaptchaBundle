<?php

namespace EWZ\Bundle\RecaptchaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class IsTrue extends Constraint
{
    public $message = 'This value is not a valid captcha.';

    public $invalidHostMessage = 'The captcha was not resolved on the right domain.';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'ewz_recaptcha.true';
    }
}
