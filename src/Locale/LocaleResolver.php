<?php

namespace EWZ\Bundle\RecaptchaBundle\Locale;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Depending on the configuration resolves the correct locale for the reCAPTCHA.
 */
final class LocaleResolver
{
    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var bool
     */
    private $useLocaleFromRequest;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param string       $defaultLocale
     * @param bool         $useLocaleFromRequest
     * @param RequestStack $requestStack
     */
    public function __construct($defaultLocale, $useLocaleFromRequest, RequestStack $requestStack)
    {
        $this->defaultLocale = $defaultLocale;
        $this->useLocaleFromRequest = $useLocaleFromRequest;
        $this->requestStack = $requestStack;
    }

    /**
     * @return string The resolved locale key, depending on configuration
     */
    public function resolve()
    {
        return $this->useLocaleFromRequest
            ? $this->requestStack->getCurrentRequest()->getLocale()
            : $this->defaultLocale;
    }
}
