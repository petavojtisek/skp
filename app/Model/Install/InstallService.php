<?php

namespace App\Model\Install;

use App\Model\Base\BaseService;
use Nette\Utils\Finder;

class InstallService extends BaseService
{
    /** @var InstallDao */
    private $installDao;

    public function __construct(InstallDao $installDao)
    {
        $this->installDao = $installDao;
    }

    public function getInstalledModules(): array
    {
        return $this->installDao->findAll() ?: [];
    }

    public function toggleInstalled(int $id, bool $state): void
    {
        $module = $this->installDao->find($id);
        if ($module) {
            $module->installed = $state ? 1 : 0;
            $this->installDao->update($module);
        }
    }

    public function uninstallModule(int $id): void
    {
        $this->installDao->delete($id);
    }

    public function getAvailableModules(): array
    {
        $modules = [];
        $path = dirname(__DIR__, 4) . '/install/modules';
        
        if (is_dir($path)) {
            foreach (Finder::findDirectories('*')->in($path) as $dir) {
                $modules[] = $dir->getBasename();
            }
        }
        
        // Filter out already installed modules
        $installed = array_map(fn($m) => $m->module_name, $this->getInstalledModules());
        return array_diff($modules, $installed);
    }
}
