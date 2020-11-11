<?php

namespace EWZ\Bundle\RecaptchaBundle\Interfaces;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface WidgetInterface
{
    public function register(ContainerBuilder $container): void;
    public function supports(int $version): bool;
}
