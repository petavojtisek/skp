<?php

namespace App\Modules\Forms\Components;

use App\Model\Element\ElementFacade;
use App\Model\Helper\IObjectControl;
use App\Modules\Forms\Model\FormsFacade;
use App\Modules\Forms\Model\FormsEntity;
use Nette\Application\UI\Control;

class FormsFrontControl extends Control implements IObjectControl
{
    private FormsFacade $facade;

    private ElementFacade $elementFacade;
    private FormControlFactory $formControlFactory;
    private int $elementId;

    private int $componentId;
    private string $name;
    private string $code;

    private ?FormsEntity $formData = null;


    public function __construct(
        FormsFacade $facade,
        ElementFacade $elementFacade,
        FormControlFactory $formControlFactory,
    ) {
        $this->formControlFactory = $formControlFactory;
        $this->facade = $facade;
        $this->elementFacade = $elementFacade;

    }

    // Factories for individual forms
    public function setComponentId(int $componentId): void
    {
        $this->componentId = $componentId;
    }

    public function setComponentInfo(string $name, string $code): void
    {
        $this->name = $name;
        $this->code = $code;
    }



    private function getFormData(): ?FormsEntity
    {
        if ($this->formData === null) {
            $this->formData = $this->facade->getForm($this->elementId);
        }
        return $this->formData;
    }

    public function render(): void
    {
        $formData = false;
            $this->elementId = $this->elementFacade->getActiveElementId($this->componentId);
        if ($this->elementId) {
            $element = $this->elementFacade->findFront($this->elementId);
            if($element) {
                $formData = $this->getFormData();
            }
        }
        if (!$formData) {
            return;
        }

        $componentName = $formData->getFormComponent();
        if ($componentName && isset($this['form'])) {
             $this['form']->render();
        }
    }

    protected function createComponentForm(): ?Control
    {

        $formData = $this->getFormData();
        if (!$formData) {
            return null;
        }

        $componentName = $formData->getFormComponent();
        return  $this->formControlFactory->create($componentName,$this->componentId,$this->elementId,$componentName);

    }
        }

interface IFormsFrontControlFactory
{
    public function create(): FormsFrontControl;
}
