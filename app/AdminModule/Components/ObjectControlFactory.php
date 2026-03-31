<?php

namespace App\AdminModule\Components;

use Nette\DI\Container;

class ObjectControlFactory
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function create(string $moduleClassName, int $componentId, string $name, string $code): ?IObjectControl
    {
        // Předpokládáme, že ModuleClassName z databáze (např. ContentVersionFacade) 
        // nám napoví, jakou továrničku hledat.
        // Pokud je v DB ContentVersionFacade, budeme hledat IContentVersionControlFactory.
        $moduleBase = str_replace('Facade', '', $moduleClassName);
        $factoryClass = "App\\Modules\\{$moduleBase}\\Components\\I{$moduleBase}ControlFactory";
        
        if (interface_exists($factoryClass)) {
            try {
                $factory = $this->container->getByType($factoryClass);
                $control = $factory->create();
                
                if ($control instanceof IObjectControl) {
                    $control->setComponentId($componentId);
                    $control->setComponentInfo($name, $code);
                    return $control;
                }
            } catch (\Nette\DI\MissingServiceException $e) {
                // Továrnička není registrovaná
            }
        }

        return null;
    }
}
