<?php

namespace EWZ\Bundle\RecaptchaBundle\Form\Type;

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueV3;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EWZRecaptchaV3Type extends AbstractEWZRecaptchaType
{

    /**
     * @var bool
     */
    private $hideBadge;

    /**
     * EWZRecaptchaV3Type constructor.
     * @param string $publicKey
     * @param bool $enabled
     * @param bool $hideBadge
     * @param string $apiHost
     */
    public function __construct($publicKey, $enabled, $hideBadge, $apiHost = 'www.google.com')
    {
        parent::__construct($publicKey, $enabled, $apiHost);
        $this->hideBadge = $hideBadge;
    }

    /**
     * {@inheritdoc}
     */
    protected function addCustomVars(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'ewz_recaptcha_hide_badge' => $this->hideBadge,
            'script_nonce_csp' => isset($options['script_nonce_csp']) ? $options['script_nonce_csp'] : '',
            'action_name' => isset($options['action_name']) ? $options['action_name'] : '',
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
    public function getParent()
    {
        return HiddenType::class;
    }

}
