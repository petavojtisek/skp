<?php

namespace App\Modules\Forms\Components;


use App\Modules\ContentVersion\Components\IObjectControl;
use App\Modules\ContentVersion\Components\IToolsControl;
use Nette\Application\UI\Control;
use Nette\DI\Container;

class FormControlFactory
{
    private Container $container;


    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Creates a form control for a given module.
     * Convention: App\Modules\{ModuleName}\Components\{ModuleName}FrontControl
     */
    public function create(
        string $formName,
        ?int $componentId = null,
        ?int $elementId = null,
        ?string $code = null
    ): Control|IFormControl|null {

        $control = null;
        $factoryClass = "App\\Modules\\Forms\\FormsComponents\\I{$formName}";
        if (interface_exists($factoryClass)) {
            try {
                $factory = $this->container->getByType($factoryClass);
                $control = $factory->create();
            } catch (\Nette\DI\MissingServiceException $e) {
                // Továrnička není registrovaná v DI
            }
        }
        if (!$control) {
            $factoryClass = "App\\Modules\\Forms\\FormsComponents\\{$formName}";
            if (class_exists($factoryClass)) {
                $control = $this->container->createInstance($factoryClass);
            }
        }


        // 3. Initialization based on implemented interface
        if ($control instanceof IFormControl) {
            if ($componentId !== null) {
                $control->setComponentId($componentId);
            }

            if ($elementId !== null) {
                $control->setElementId($elementId);
            }


            $control->setCode($code);
            $control->setFormName($formName);

        }

        if ($control instanceof IFormControl) {
            return $control;
        }

        // Use DI container to create instance with all dependencies
        return null;
    }
}

interface IFormControlFactory
{
    public function create(): FormControlFactory;
}

