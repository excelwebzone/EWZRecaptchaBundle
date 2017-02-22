<?php

namespace EWZ\Bundle\RecaptchaBundle\Tests\Locale;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
class LocaleResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function resolveWithLocaleFromRequest()
    {
        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('getLocale');

        $requestStack = $this->createMock(RequestStack::class);
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
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->expects($this->never())
            ->method('getCurrentRequest');

        $resolver = new LocaleResolver('foo', false, $requestStack);
        $resolver->resolve();
    }
}
