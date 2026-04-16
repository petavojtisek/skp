<?php

namespace App\Modules\Forms\Components;

use App\Modules\Forms\Model\FormsFacade;
use App\Modules\Forms\Model\FormsEntity;
use App\Modules\Forms\FormsComponents\IContactFormFactory;
use Nette\Application\UI\Control;

class FormsFrontControl extends Control
{
    private FormsFacade $facade;
    private int $elementId;
    private ?FormsEntity $formData = null;

    // Factories for individual forms
    private IContactFormFactory $contactFormFactory;

    public function __construct(
        int $elementId,
        FormsFacade $facade,
        IContactFormFactory $contactFormFactory
    ) {
        $this->elementId = $elementId;
        $this->facade = $facade;
        $this->contactFormFactory = $contactFormFactory;
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
        $formData = $this->getFormData();
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

        switch ($componentName) {
            case 'ContactForm':
                return $this->contactFormFactory->create();
            // Add other forms here
            default:
                return null;
        }
    }
}

interface IFormsFrontControlFactory
{
    public function create(int $elementId): FormsFrontControl;
}
