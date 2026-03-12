<?php

namespace App\AdminModule\Presenters;

use App\Model\Log\LogFacade;

final class LogPresenter extends AdminPresenter
{
    /** @var LogFacade @inject */
    public $logFacade;

    public function renderDefault(): void
    {
        $this->template->title = 'Systémový log';
        $this->template->logs = $this->logFacade->getAllLogs();
    }

    public function renderDetail(int $id): void
    {
        $this->template->title = 'Detail logu #' . $id;
        // Logic to show JSON data in detail if needed, but for now we list all in default
    }
}
