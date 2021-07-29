<?php

namespace EWZ\Bundle\RecaptchaBundle\Validator\Constraints;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class IsTrueV3 extends IsTrue
{
    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'ewz_recaptcha.v3.true';
    }
}
