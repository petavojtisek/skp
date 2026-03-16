<?php

namespace App\AdminModule\Presenters;

final class FilesPresenter extends AdminPresenter
{
    public function renderDefault(): void
    {
        $this->template->title = 'Správce souborů';
    }
}
