<?php

namespace App\AdminModule\Presenters;

use App\Model\Install\InstallFacade;
use Nette\Application\UI\Form;

final class InstallPresenter extends AdminPresenter
{
    /** @var InstallFacade @inject */
    public $installFacade;

    /** @var \App\Model\Module\ModuleFacade @inject */
    public $moduleFacade;

    /** @var int|null @persistent */
    public $id;

    public function renderDefault(): void
    {
        $this->template->title = 'Instalované moduly';
        $installedModules = $this->installFacade->getInstalledModules();
        $this->template->installedModules = $installedModules;
        $this->template->availableModules = $this->installFacade->getAvailableModules();

        $activeStates = [];
        foreach ($installedModules as $m) {
            $moduleEntity = $this->moduleFacade->getModuleByInstallId($m->getId());
            $activeStates[$m->getId()] = $moduleEntity ? ($moduleEntity->getModuleActive() === 'Y') : false;
        }
        $this->template->activeStates = $activeStates;
    }

    public function handleToggleActive(int $id, bool $state = false): void
    {
        $this->installFacade->toggleInstalled($id, $state);
        if ($this->isAjax()) {
            $installedModules = $this->installFacade->getInstalledModules();
            $this->template->installedModules = $installedModules;
            
            $activeStates = [];
            foreach ($installedModules as $m) {
                $moduleEntity = $this->moduleFacade->getModuleByInstallId($m->getId());
                $activeStates[$m->getId()] = $moduleEntity ? ($moduleEntity->getModuleActive() === 'Y') : false;
            }
            $this->template->activeStates = $activeStates;
            
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
     * Signal to install module
     */
    public function handleInstall(string $name): void
    {
        try {
            $this->installFacade->installModule($name);
            $this->flashMessage('Modul ' . $name . ' byl úspěšně nainstalován.', 'success');
        } catch (\Exception $e) {
            $this->flashMessage('Chyba při instalaci modulu: ' . $e->getMessage(), 'danger');
        }
        $this->redirect('this');
    }
}
