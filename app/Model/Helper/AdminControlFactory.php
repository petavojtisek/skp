<?php

namespace App\Model\Helper;

use Nette\DI\Container;
use Nette\Application\UI\Control;

class AdminControlFactory
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Creates an admin control for a given system module.
     * Convention: App\Modules\{ModuleName}\Components\{ModuleName}AdminControl
     */
    public function create(string $moduleCode): ?Control
    {
        // Normalize module code (e.g. webTexts -> WebTexts)
        $moduleName = ucfirst($moduleCode);
        $className = "App\\Modules\\{$moduleName}\\Components\\{$moduleName}AdminControl";
        
        if (!class_exists($className)) {
            return null;
        }

        // Use DI container to create instance with all dependencies automatically
        return $this->container->createInstance($className);
    }
}
