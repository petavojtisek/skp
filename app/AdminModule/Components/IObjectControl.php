<?php

namespace App\AdminModule\Components;

use Nette\Application\UI\Control;

interface IObjectControl
{
    /**
     * Set component instance data
     */
    public function setComponentId(int $componentId): void;

    /**
     * Set component name and code for heading
     */
    public function setComponentInfo(string $name, string $code): void;

    /**
     * Render the component
     */
    public function render(): void;
}
