<?php

namespace EWZ\Tests\Bundle\RecaptchaBundle\Locale;

use EWZ\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use Mockery;
use PHPUnit\Framework\TestCase;

class LocaleResolverTest extends TestCase
{
    /**
     * @test
     */
    public function resolveWithLocaleFromRequest()
    {
        $request = Mockery::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getLocale')->once()->andReturn('foo');

        $requestStack = Mockery::mock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack->shouldReceive('getCurrentRequest')->once()->andReturn($request);

        $resolver = new LocaleResolver('foo', true, $requestStack);
        $this->assertSame('foo', $resolver->resolve());
    }

    /**
     * @test
     */
    public function resolveWithDefaultLocale()
    {
        $requestStack = Mockery::mock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack->shouldReceive('getCurrentRequest')->never();

        $resolver = new LocaleResolver('foo', false, $requestStack);
        $this->assertSame('foo', $resolver->resolve());
    }
}
