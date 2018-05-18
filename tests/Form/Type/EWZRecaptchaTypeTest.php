<?php

namespace EWZ\Tests\Bundle\RecaptchaBundle\Form\Type;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EWZRecaptchaTypeTest extends TestCase
{
    /** @var EWZRecaptchaType */
    protected $type;

    protected function setUp()
    {
        $requestStack = $this->createMock('Symfony\Component\HttpFoundation\RequestStack');
        $localeResolver = new LocaleResolver('de', false, $requestStack);
        $this->type = new EWZRecaptchaType('key', true, true, $localeResolver, 'www.google.com');
    }

    /**
     * @test
     */
    public function buildView()
    {
        $view = new FormView();

        $form = $this->createMock('Symfony\Component\Form\FormInterface');

        $this->assertArrayNotHasKey('ewz_recaptcha_enabled', $view->vars);
        $this->assertArrayNotHasKey('ewz_recaptcha_ajax', $view->vars);

        $this->type->buildView($view, $form, array());

        $this->assertTrue($view->vars['ewz_recaptcha_enabled']);
        $this->assertTrue($view->vars['ewz_recaptcha_ajax']);
    }

    /**
     * @test
     */
    public function getParent()
    {
        $this->assertSame('Symfony\Component\Form\Extension\Core\Type\TextType', $this->type->getParent());
    }

    /**
     * @test
     */
    public function getPublicKey()
    {
        $this->assertSame('key', $this->type->getPublicKey());
    }

    /**
     * @test
     */
    public function configureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->type->configureOptions($optionsResolver);

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
    public function getBlockPrefix()
    {
        $this->assertEquals('ewz_recaptcha', $this->type->getBlockPrefix());
    }
}
