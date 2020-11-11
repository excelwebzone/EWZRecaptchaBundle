<?php

namespace EWZ\Bundle\RecaptchaBundle;

use EWZ\Bundle\RecaptchaBundle\DependencyInjection\CompilerPass\WidgetCompilerPass;
use EWZ\Bundle\RecaptchaBundle\Resolver\WidgetResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EWZRecaptchaBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new WidgetCompilerPass());
    }
}
