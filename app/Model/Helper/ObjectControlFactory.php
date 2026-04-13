<?php

namespace App\Model\Helper;

use Nette\DI\Container;
use Nette\Application\UI\Control;

class ObjectControlFactory
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Creates an admin control for a given module.
     * Can return IObjectControl (with ID) or IToolsControl (simple).
     */
    public function create(string $moduleClassName, ?int $componentId = null, ?string $name = null, ?string $code = null): ?Control
    {
        $control = null;


        // 1. Try explicit interface factory
        $factoryClass = "App\\Modules\\{$moduleClassName}\\Components\\I{$moduleClassName}AdminControlFactory";

        if (interface_exists($factoryClass)) {
            try {
                $factory = $this->container->getByType($factoryClass);
                $control = $factory->create();
            } catch (\Nette\DI\MissingServiceException $e) {
                // Factory not registered in DI
            }
        }

        // 2. Try convention-based creation
        if (!$control) {
            $className = "App\\Modules\\{$moduleClassName}\\Components\\{$moduleClassName}AdminControl";
            if (class_exists($className)) {
                $control = $this->container->createInstance($className);
            }
        }

        // 3. Initialization based on implemented interface
        if ($control instanceof IObjectControl) {
            if ($componentId !== null) {
                $control->setComponentId($componentId);
            }
            if ($name !== null && $code !== null) {
                $control->setComponentInfo($name, $code);
            }
        }elseif($control instanceof IToolsControl) {
            // No additional initialization needed for tools control
              $control->setCode($moduleClassName);
        }

        // Return if it's a valid admin control (either Object or Tool)
        if ($control instanceof IObjectControl || $control instanceof IToolsControl) {
            return $control;
        }

        return null;
    }
}
