<?php

namespace App\Model\Install;

use App\Model\Module\ModuleFacade;

class InstallFacade
{
    private InstallService $installService;
    private InstallManager $installManager;
    private ModuleFacade $moduleFacade;

    public function __construct(
        InstallService $installService, 
        InstallManager $installManager,
        ModuleFacade $moduleFacade
    ) {
        $this->installService = $installService;
        $this->installManager = $installManager;
        $this->moduleFacade = $moduleFacade;
    }

    public function getInstalledModules(): array
    {
        return $this->installService->getInstalledModules();
    }

    public function toggleInstalled(int $id, bool $state): void
    {
        // Přepínáme aktivitu modulu v tabulce module
        $this->moduleFacade->toggleActiveByInstallId($id, $state);
    }

    public function uninstallModule(int $id): void
    {
        $this->installManager->uninstall($id);
    }

    public function getAvailableModules(): array
    {
        return $this->installService->getAvailableModules();
    }

    public function installModule(string $name): void
    {
        $this->installManager->install($name);
    }

    public function findByModuleName(string $name): ?InstallEntity
    {
        return $this->installService->findByModuleName($name);
    }

    public function saveInstall(InstallEntity $entity): InstallEntity
    {
        return $this->installService->save($entity);
    }
}
