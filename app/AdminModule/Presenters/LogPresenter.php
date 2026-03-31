<?php

namespace App\AdminModule\Presenters;

use App\Model\Log\LogFacade;

final class LogPresenter extends AdminPresenter
{
    /** @var LogFacade @inject */
    public $logFacade;

    /** @var int @persistent */
    public $page = 1;

    /** @var string|null @persistent */
    public $search = null;

    /** @var string|null @persistent */
    public $module = null;

    /** @var string|null @persistent */
    public $dateFrom = null;

    /** @var string|null @persistent */
    public $dateTo = null;

    public function renderDefault(): void
    {
        $this->template->title = 'Systémový log';
        
        $limit = 20;
        $offset = ($this->page - 1) * $limit;
        
        $count = $this->logFacade->countLogs($this->search, $this->module, $this->dateFrom, $this->dateTo);
        $logs = $this->logFacade->getLogs($limit, $offset, $this->search, $this->module, $this->dateFrom, $this->dateTo);

        $this->template->logs = $logs;
        $this->template->search = $this->search;
        $this->template->selectedModule = $this->module;
        $this->template->dateFrom = $this->dateFrom;
        $this->template->dateTo = $this->dateTo;
        
        // Filter options
        $this->template->modules = $this->logFacade->getUniqueModules();
        
        // Pagination data
        $this->template->page = $this->page;
        $this->template->lastPage = ceil($count / $limit);
        $this->template->totalCount = $count;
    }

    public function handleReset(): void
    {
        $this->page = 1;
        $this->search = null;
        $this->module = null;
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->redirect('this');
    }
}
