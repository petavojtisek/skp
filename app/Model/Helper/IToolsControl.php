<?php

namespace App\Model\Helper;


use Nette\Application\UI\Control;

interface IToolsControl
{

    /**
     * Render the component
     */
    public function render(): void;

    public function setCode(string $code): void;
}
