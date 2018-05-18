<?php

namespace EWZ\Tests\Bundle\RecaptchaBundle\Locale;

use EWZ\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use PHPUnit\Framework\TestCase;

class LocaleResolverTest extends TestCase
{
    /**
     * @test
     */
    public function resolveWithLocaleFromRequest()
    {
        $request = $this->createMock('Symfony\Component\HttpFoundation\Request');
        $request->expects($this->once())->method('getLocale');

        $requestStack = $this->createMock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $resolver = new LocaleResolver('foo', true, $requestStack);
        $resolver->resolve();
    }

    /**
     * @test
     */
    public function resolveWithDefaultLocale()
    {
        $requestStack = $this->createMock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack
            ->expects($this->never())
            ->method('getCurrentRequest');

        $resolver = new LocaleResolver('foo', false, $requestStack);
        $resolver->resolve();
    }
}
