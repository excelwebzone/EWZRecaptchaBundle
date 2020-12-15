<?php

namespace EWZ\Bundle\RecaptchaBundle\Factory;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;
use Symfony\Component\Form\FormFactoryInterface;

class EWZRecaptchaV2FormBuilderFactory
{
    private $builder;

    public function __construct(FormFactoryInterface $builder)
    {
        $this->builder = $builder;
    }

    public function get(array $options = array())
    {
        $constraint = array(
            'constraints' => array(
                new IsTrue(),
            ),
        );

        return $this->builder->createBuilder(EWZRecaptchaType::class, null, array_merge($options, $constraint));
    }
}
