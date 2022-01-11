<?php

namespace EWZ\Bundle\RecaptchaBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EWZRecaptchaV3Type extends AbstractEWZRecaptchaType
{
    public const DEFAULT_ACTION_NAME = 'form';

    /** @var bool */
    private $hideBadge;

    /**
     * EWZRecaptchaV3Type constructor.
     *
     * @param string $publicKey
     * @param bool $enabled
     * @param bool   $hideBadge
     * @param string $apiHost
     */
    public function __construct(string $publicKey, bool $enabled, bool $hideBadge, string $apiHost = 'www.google.com')
    {
        parent::__construct($publicKey, $enabled, $apiHost);

        $this->hideBadge = $hideBadge;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'mapped' => false,
            'validation_groups' => ['Default'],
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
    protected function addCustomVars(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'ewz_recaptcha_hide_badge' => $this->hideBadge,
            'script_nonce_csp' => $options['script_nonce_csp'] ?? '',
            'action_name' => $options['action_name'] ?? '',
        ]);
    }
}
