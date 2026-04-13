<?php

namespace App\AdminModule\Presenters;

use App\Model\Helper\ObjectControlFactory;
use App\Model\Module\ModuleFacade;

use Nette\ComponentModel\IComponent;

final class ToolsPresenter extends AdminPresenter
{
    /** @var ModuleFacade @inject */
    public $moduleFacade;

    /** @var ObjectControlFactory @inject */
    public $adminControlFactory;

    /** @var string|null @persistent */
    public $module = null;

    public $activeControl = false;


    /** @var array Seznam ověřených a dostupných modulů (module_code => module_entity) */
    private array $availableSystemModules = [];

    public $showTool = false;

    public function startup(): void
    {
        parent::startup();
        $this->loadAvailableModules();

    }

    public function beforeRender()
    {


        if($this->activeControl){
            $this->template->setFile(__DIR__.'/../templates/Tools/detail.latte');
            $this->template->module = $this->activeControl;
        }

        $this->template->showTool = $this->showTool;
    }


    private function loadAvailableModules(): void
    {

        $modules = $this->moduleFacade->findActiveByType(C_TOOLS);
        foreach ($modules as $mod) {
            // 1. Kontrola práv na výpis (list) - zde stále používáme code_name pro práva
            if (!$this->loggedUserEntity->hasModuleRight($mod->getModuleCodeName(), 'list')) {
                continue;
            }
            $this->availableSystemModules[$mod->getModuleClassName()] = $mod;
        }
    }

    public function renderDefault(): void
    {
        $this->template->title = 'Nástroje';


        // Znovu profiltrujeme moduly, aby se zobrazily jen ty, které mají i fyzický soubor
        $finalModules = [];
        foreach ($this->availableSystemModules as $code => $mod) {
            // Jen pro dashboard zkusíme vytvořit komponentu, abychom potvrdili existenci
            if ($this->getComponent($mod->getModuleClassName(), false)) {
                $finalModules[$mod->getModuleClassName()] = $mod;
            }
        }


        $this->template->systemModules = $finalModules;
    }

    public function actionDetail(string $module): void
    {
        if (!isset($this->availableSystemModules[$module]) || !$this->getComponent($module, false)) {
            $this->flashMessage('Modul není dostupný nebo nemáte dostatečná oprávnění.', 'danger');
            $this->redirect('default');
        }

        $this->module = $module;
        $this->template->title = 'Detail nástroje: ' . $this->availableSystemModules[$module]->getModuleName();
    }

    protected function createComponent($name): ?IComponent
    {
        // Nejdříve zkusíme standardní parent logic (pro fixní komponenty)
        $component = parent::createComponent($name);
        if ($component) {
            return $component;
        }

        // Pokud jde o modul ze seznamu, použijeme univerzální factory
        if (isset($this->availableSystemModules[$name])) {
            return $this->adminControlFactory->create($name);
        }

        return null;
    }
}
