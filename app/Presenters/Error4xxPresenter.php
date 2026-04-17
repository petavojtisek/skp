<?php

namespace App\Presenters;

use Nette;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;


final class Error4xxPresenter extends Presenter
{
    public function renderDefault(Nette\Application\BadRequestException $exception): void
    {
        // Zjistíme kód (404, 403, atd.)
        $code = $exception->getCode();

        // Zkusíme najít šablonu 404.latte, 403.latte, jinak 4xx.latte
        $file = __DIR__ . "/templates/Error/{$code}.latte";
        $this->setLayout(false);
        $this->template->setFile(is_file($file) ? $file : __DIR__ . '/templates/Error/4xx.latte');
    }
}
