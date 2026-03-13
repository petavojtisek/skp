<?php

namespace App\AdminModule\Presenters;

use App\Model\Install\InstallFacade;
use Nette\Application\UI\Form;

final class InstallPresenter extends AdminPresenter
{
    /** @var InstallFacade @inject */
    public $installFacade;

    /** @var int|null @persistent */
    public $id;

    public function renderDefault(): void
    {
        $this->template->title = 'Instalované moduly';
        $this->template->installedModules = $this->installFacade->getInstalledModules();
        $this->template->availableModules = $this->installFacade->getAvailableModules();
    }

    /**
     * AJAX signal to toggle module active status
     */
    public function handleToggleActive(int $id, bool $state = false): void
    {
        $this->installFacade->toggleInstalled($id, $state);
        if ($this->isAjax()) {
            $this->template->installedModules = $this->installFacade->getInstalledModules();
            $this->redrawControl('installedTableSnippet');
        } else {
            $this->redirect('this');
        }
    }

    /**
     * AJAX signal to uninstall module (database record removal)
     */
    public function handleUninstall(int $id): void
    {
        $this->installFacade->uninstallModule($id);
        $this->flashMessage('Modul byl odinstalován.');
        if ($this->isAjax()) {
            $this->template->installedModules = $this->installFacade->getInstalledModules();
            $this->template->availableModules = $this->installFacade->getAvailableModules();
            $this->redrawControl('installedTableSnippet');
            $this->redrawControl('availableTableSnippet');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }

    /**
     * Signal to install module (placeholder)
     */
    public function handleInstall(string $name): void
    {
        // TODO: Implement actual installation logic (copying, SQL execution)
        $this->flashMessage('Instalace modulu ' . $name . ' zatím není implementována.', 'info');
        $this->redirect('this');
    }
}
