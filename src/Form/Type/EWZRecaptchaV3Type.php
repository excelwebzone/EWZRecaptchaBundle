<?php

namespace EWZ\Bundle\RecaptchaBundle\Form\Type;

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueV3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EWZRecaptchaV3Type extends AbstractType
{
    /** 
     * @var string 
     */
    private $publicKey;

    /** 
     * @var bool 
     */
    private $hideBadge;

    /** 
     * @var string 
     */
    private $apiHost;
    
    /**
     * RecaptchaType constructor.
     *
     * @param string $publicKey
     * @param bool $hideBadge
     * @param string $apiHost
     */
    public function __construct(string $publicKey, bool $hideBadge, string $apiHost = 'www.google.com')
    {
        $this->publicKey = $publicKey;
        $this->hideBadge = $hideBadge;
        $this->apiHost = $apiHost;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'ewz_recaptcha_public_key' => $this->publicKey,
            'ewz_recaptcha_hide_badge' => $this->hideBadge,
            'ewz_recaptcha_apihost' => $this->apiHost,
            'script_nonce_csp' => $options['script_nonce_csp'] ?? '',
            'action_name' => $options['action_name'] ?? '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'mapped' => false,
            'constraints' => [
                new IsTrueV3()
            ],
            'validation_groups' => [ 'Default' ],
            'script_nonce_csp' => '',
            'action_name' => 'form',
        ]);

        $resolver->setAllowedTypes('script_nonce_csp', 'string');
        $resolver->setAllowedTypes('action_name', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return HiddenType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ewz_recaptcha';
    }

}
