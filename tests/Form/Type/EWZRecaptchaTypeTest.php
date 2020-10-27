<?php

namespace EWZ\Tests\Bundle\RecaptchaBundle\Form\Type;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EWZRecaptchaTypeTest extends TestCase
{
    /** @var EWZRecaptchaType */
    protected $type;

    protected function getType($enabled = true)
    {
        $requestStack = $this->createMock(RequestStack::class);
        $localeResolver = new LocaleResolver('de', false, $requestStack);
        return (new EWZRecaptchaType('key', $enabled, true, $localeResolver, 'www.google.com'));
    }

    /**
     * @test
     */
    public function buildView()
    {
        $view = new FormView();

        /** @var FormInterface $form */
        $form = $this->createMock(FormInterface::class);

        $this->assertArrayNotHasKey('ewz_recaptcha_enabled', $view->vars);
        $this->assertArrayNotHasKey('ewz_recaptcha_ajax', $view->vars);

        $this->getType()->buildView($view, $form, array());

        $this->assertTrue($view->vars['ewz_recaptcha_enabled']);
        $this->assertTrue($view->vars['ewz_recaptcha_ajax']);
    }

    /**
     * @test
     */
    public function getParent()
    {
        $this->assertSame(TextType::class, $this->getType()->getParent());
    }

    /**
     * @test
     */
    public function getPublicKey()
    {
        $this->assertSame('key', $this->getType()->getPublicKey());
    }

    /**
     * @test
     */
    public function configureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->getType()->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve();

        $expected = array(
            'compound' => false,
            'language' => 'de',
            'public_key' => null,
            'url_challenge' => null,
            'url_noscript' => null,
            'attr' => array(
                'options' => array(
                    'theme' => 'light',
                    'type' => 'image',
                    'size' => 'normal',
                    'callback' => null,
                    'expiredCallback' => null,
                    'bind' => null,
                    'defer' => false,
                    'async' => false,
                    'badge' => null,
                ),
            ),
        );

        $this->assertSame($expected, $options);
    }

    /**
     * @test
     */
    public function configureOptionsDisabled()
    {
        $optionsResolver = new OptionsResolver();

        $this->getType(false)->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve();

        $expected = array(
            'compound' => false,
            'language' => 'de',
            'public_key' => null,
            'url_challenge' => null,
            'url_noscript' => null,
            'attr' => array(
                'options' => array(
                    'theme' => 'light',
                    'type' => 'image',
                    'size' => 'normal',
                    'callback' => null,
                    'expiredCallback' => null,
                    'bind' => null,
                    'defer' => false,
                    'async' => false,
                    'badge' => null,
                ),
            ),
            'validation_groups' => false,
        );

        $this->assertSame($expected, $options);
    }

    /**
     * @test
     */
    public function getBlockPrefix()
    {
        $this->assertEquals('ewz_recaptcha', $this->getType()->getBlockPrefix());
    }
}
