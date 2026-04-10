<?php

namespace App\Model\Helper;

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

    public function render(): void;
}
