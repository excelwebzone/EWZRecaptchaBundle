<?php

namespace EWZ\Bundle\RecaptchaBundle\Tests\Form\Type;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
class EWZRecaptchaTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getBlockPrefix()
    {
        $requestStack = $this->createMock(RequestStack::class);
        $localeResolver = new LocaleResolver('foo', false, $requestStack);

        $type = new EWZRecaptchaType('foo', true, true, $localeResolver);
        $this->assertEquals('ewz_recaptcha', $type->getBlockPrefix());
    }
}
