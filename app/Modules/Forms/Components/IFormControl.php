<?php

namespace App\Modules\Forms\Components;

interface IFormControl
{

    /**
     * Render the component
     */
    public function render(): void;

    public function setComponentId(int $componentId): void;

    public function setElementId(int $elementId): void;

    public function setCode(string $code): void;
    public function setFormName(string $code): void;


}
