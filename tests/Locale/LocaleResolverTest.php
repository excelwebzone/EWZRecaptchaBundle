<?php

namespace EWZ\Tests\Bundle\RecaptchaBundle\Locale;

use EWZ\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LocaleResolverTest extends TestCase
{
    /**
     * @test
     */
    public function resolveWithLocaleFromRequest(): void
    {
        $locale = 'locale';
        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('getLocale')->willReturn($locale);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $resolver = new LocaleResolver('foo', true, $requestStack);
        self::assertSame($locale, $resolver->resolve());
    }

    /**
     * @test
     */
    public function resolveWithDefaultLocale(): void
    {
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->expects($this->never())
            ->method('getCurrentRequest');

        $resolver = new LocaleResolver('foo', false, $requestStack);
        $resolver->resolve();
    }
}
