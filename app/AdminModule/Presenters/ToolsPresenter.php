<?php

namespace App\AdminModule\Presenters;

final class ToolsPresenter extends AdminPresenter
{
    public function renderDefault(): void
    {
        $this->template->title = 'Nástroje';
    }
}
