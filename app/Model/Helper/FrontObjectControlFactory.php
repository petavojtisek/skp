<?php

namespace App\Model\Helper;

use Nette\DI\Container;
use Nette\Application\UI\Control;

class FrontObjectControlFactory
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Creates a frontend control for a given module.
     * Convention: App\Modules\{ModuleName}\Components\{ModuleName}FrontControl
     */
    public function create(string $moduleName, ?int $componentId = null, ?string $name = null, ?string $code = null): Control|IObjectControl|null
    {
        $control = null;
        $factoryClass = "App\\Modules\\{$moduleName}\\Components\\I{$moduleName}FrontControlFactory";
        if (interface_exists($factoryClass)) {
            try {
                $factory = $this->container->getByType($factoryClass);
                $control = $factory->create();
            } catch (\Nette\DI\MissingServiceException $e) {
                // Továrnička není registrovaná v DI
            }
        }
        if (!$control) {
            $className = "App\\Modules\\{$moduleName}\\Components\\{$moduleName}FrontControl";
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
        }

        if ($control instanceof IObjectControl || $control instanceof IToolsControl) {
            return $control;
        }

        // Use DI container to create instance with all dependencies
        return null;
    }
}
