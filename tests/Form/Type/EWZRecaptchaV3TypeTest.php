<?php

namespace EWZ\Tests\Bundle\RecaptchaBundle\Form\Type;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaV3Type;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EWZRecaptchaV3TypeTest extends TestCase
{
    /** @var EWZRecaptchaV3Type */
    protected $type;

    protected function setUp(): void
    {
        $requestStack = $this->createMock(RequestStack::class);
        $this->type = new EWZRecaptchaV3Type('key', true, true, 'www.google.com');
    }

    /**
     * @test
     */
    public function buildView(): void
    {
        $view = new FormView();

        /** @var FormInterface $form */
        $form = $this->createMock(FormInterface::class);

        $this->assertArrayNotHasKey('ewz_recaptcha_enabled', $view->vars);
        $this->assertArrayNotHasKey('ewz_recaptcha_hide_badge', $view->vars);

        $this->type->buildView($view, $form, array());

        $this->assertTrue($view->vars['ewz_recaptcha_enabled']);
        $this->assertTrue($view->vars['ewz_recaptcha_hide_badge']);
    }

    /**
     * @test
     */
    public function getParent(): void
    {
        $this->assertSame(HiddenType::class, $this->type->getParent());
    }

    /**
     * @test
     */
    public function getPublicKey(): void
    {
        $this->assertSame('key', $this->type->getPublicKey());
    }

    /**
     * @test
     */
    public function configureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->type->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve();

        $expected = array(
            'label' => false,
            'mapped' => false,
            'validation_groups' => [ 'Default' ],
            'script_nonce_csp' => '',
            'action_name' => 'form',
        );

        $this->assertSame($expected, $options);
    }

    /**
     * @test
     */
    public function getBlockPrefix(): void
    {
        $this->assertEquals('ewz_recaptcha', $this->type->getBlockPrefix());
    }
}
