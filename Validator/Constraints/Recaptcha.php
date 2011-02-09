<?php

namespace Bundle\RecaptchaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Recaptcha extends Constraint
{
    public $message = 'This value is not a valid captcha';

    public function validatedBy()
    {
        return 'validator.recaptcha';
    }

    /**
     * {@inheritdoc}
     */
    public function targets()
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }
}
