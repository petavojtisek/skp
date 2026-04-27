<?php

namespace App\AdminModule\Presenters;

final class DashboardPresenter extends AdminPresenter
{
    /** @var \App\Modules\FormsData\Components\IFormsDataDashboardControlFactory @inject */
    public $formsDataDashboardFactory;

    public $activeControl = false;
    public function renderDefault(): void
    {
        $this->template->title = 'Dashboard';
    }

    protected function createComponentInquiries(): \App\Modules\FormsData\Components\FormsDataDashboardControl
    {
        return $this->formsDataDashboardFactory->create('Kontaktní formulář', 5);
    }

    public function beforeRender()
    {
        $this->template->activeControl = $this->activeControl;
    }
}
