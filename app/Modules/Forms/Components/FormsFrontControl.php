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

    /** @persistent */
    public int $formElementId;

    /** @persistent */
    public int $page_id;

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
            $this->formData = $this->facade->getForm($this->formElementId);
        }
        return $this->formData;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/../templates/Forms/FormsControl.latte');
        $this->template->render();

    }



    protected function createComponentForm(): ?Control
    {

        if(empty($this->formElementId)) {
            $this->formElementId = $this->elementFacade->getActiveElementId($this->componentId);
        }

        if ($this->formElementId) {
            $formData = $this->getFormData();
            if ($formData) {
                $componentName = $formData->getFormComponent();
                return $this->formControlFactory->create(
                    $componentName,
                    $this->componentId,
                    $this->formElementId,
                    $componentName
                );
            }
        }

        return null;
    }
 }

interface IFormsFrontControlFactory
{
    public function create(): FormsFrontControl;
}
