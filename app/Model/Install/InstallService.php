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

    public function findByModuleName(string $name): ?InstallEntity
    {
        return $this->installDao->findOneBy(['module_name' => $name]);
    }

    public function find(int $id): ?InstallEntity
    {
        return $this->installDao->find($id);
    }

    public function save(InstallEntity $entity): InstallEntity
    {
        return $this->installDao->save($entity);
    }

    public function delete(int $id): void
    {
        $this->installDao->delete($id);
    }

    public function getAvailableModules(): array
    {
        $modules = [];
        $path = dirname(__DIR__, 3) . '/install/modules';
        
        if (is_dir($path)) {
            foreach (Finder::findDirectories('*')->in($path) as $dir) {
                $modules[] = $dir->getBasename();
            }
        }
        
        $installed = array_map(fn($m) => $m->getModuleName(), $this->getInstalledModules());
        return array_diff($modules, $installed);
    }
}
