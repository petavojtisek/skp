<?php

namespace App\Model\Helper;

use Nette\DI\Container;
use Nette\Application\UI\Control;

class FrontControlFactory
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
    public function create(string $moduleName): ?Control
    {
        $className = "App\\Modules\\{$moduleName}\\Components\\{$moduleName}FrontControl";
        
        if (!class_exists($className)) {
            return null;
        }

        // Use DI container to create instance with all dependencies
        return $this->container->createInstance($className);
    }
}
