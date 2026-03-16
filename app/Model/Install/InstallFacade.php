<?php

namespace App\Model\Install;

class InstallFacade
{
    /** @var InstallService */
    private $installService;

    public function __construct(InstallService $installService)
    {
        $this->installService = $installService;
    }

    public function getInstalledModules(): array
    {
        return $this->installService->getInstalledModules();
    }

    public function toggleInstalled(int $id, bool $state): void
    {
        $this->installService->toggleInstalled($id, $state);
    }

    public function uninstallModule(int $id): void
    {
        $this->installService->uninstallModule($id);
    }

    public function getAvailableModules(): array
    {
        return $this->installService->getAvailableModules();
    }

    public function getModuleByInstallId(int $installId): ?array
    {
        return $this->installService->getModuleByInstallId($installId);
    }
}
