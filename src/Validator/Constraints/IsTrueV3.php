<?php

namespace EWZ\Bundle\RecaptchaBundle\Validator\Constraints;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class IsTrueV3 extends IsTrue
{
    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'ewz_recaptcha.v3.true';
    }
}
