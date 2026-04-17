<?php

namespace App\Modules\Forms\FormsComponents;

use App\Modules\Forms\Components\IFormControl;
use App\Modules\FormsData\Model\FormsDataFacade;
use Nette\Application\UI\Control;

class BaseForm extends Control implements IFormControl
{

    protected int $componentId;
    protected int $elementId;

    protected ?string $code = null;
    protected ?string $formName= null;

    protected FormsDataFacade $formsDataFacade;

    public function __construct(FormsDataFacade $formsDataFacade)
    {
        $this->formsDataFacade = $formsDataFacade;
    }

    public function setElementId(int $elementId):void
    {
        $this->elementId = $elementId;

    }

    public function setComponentId(int $componentId):void
    {
        $this->componentId = $componentId;

    }
    public function setCode(?string $componentId):void
    {
        $this->code = $componentId;

    }
    public function setFormName(?string $formName):void
    {
        $this->formName = $formName;

    }

    public function render(): void
    {

    }




}
