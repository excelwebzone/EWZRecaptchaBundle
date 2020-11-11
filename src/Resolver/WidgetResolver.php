<?php

namespace EWZ\Bundle\RecaptchaBundle\Resolver;

use EWZ\Bundle\RecaptchaBundle\Interfaces\WidgetInterface;

class WidgetResolver
{
    /**
     * List of all available widgets
     * @var WidgetInterface[]
     */
    private $widgets = array();

    /**
     * Add a widget to the list
     * @param WidgetInterface $widget
     */
    public function addWidget(WidgetInterface $widget): void
    {
        $this->widgets[] = $widget;
    }

    /**
     *
     * @param int $version
     * @return WidgetInterface
     */
    public function getWidget(int $version): WidgetInterface
    {
        foreach ($this->widgets as $widget) {
            if ($widget->supports($version)) {
                return $widget;
            }
        }
    }

}