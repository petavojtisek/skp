<?php

namespace App\Model\Install;

use App\Model\Module\ModuleFacade;
use App\Model\ModulePermission\ModulePermissionFacade;
use App\Model\ModuleGroupRight\ModuleGroupRightFacade;
use Dibi\Connection;
use Nette\Utils\FileSystem;

class InstallManager
{
    private Connection $db;
    private InstallService $installService;
    private ModuleFacade $moduleFacade;
    private ModulePermissionFacade $modulePermissionFacade;
    private ModuleGroupRightFacade $moduleGroupRightFacade;
    private string $tempDir;

    public function __construct(
        string $tempDir,
        Connection $db,
        InstallService $installService,
        ModuleFacade $moduleFacade,
        ModulePermissionFacade $modulePermissionFacade,
        ModuleGroupRightFacade $moduleGroupRightFacade
    ) {
        $this->tempDir = $tempDir;
        $this->db = $db;
        $this->installService = $installService;
        $this->moduleFacade = $moduleFacade;
        $this->modulePermissionFacade = $modulePermissionFacade;
        $this->moduleGroupRightFacade = $moduleGroupRightFacade;
    }

    public function install(string $name): void
    {
        $root = dirname(__DIR__, 3);
        $srcPath = $root . '/install/modules/' . $name;
        $destPath = $root . '/app/Modules/' . $name;
        
        if (!is_dir($srcPath)) {
            throw new \Exception("Zdrojový adresář modulu $name nebyl nalezen.");
        }

        $filesCopied = false;
        $this->db->begin();

        try {
            // 1. Copy files
            if (is_dir($srcPath . '/src')) {
                FileSystem::copy($srcPath . '/src', $destPath);
                $filesCopied = true;
            }

            // 2. Prepare context for install.php
            $db = $this->db;
            $installService = $this->installService;
            $moduleFacade = $this->moduleFacade;
            $modulePermissionFacade = $this->modulePermissionFacade;
            $moduleGroupRightFacade = $this->moduleGroupRightFacade;

            // 3. Run script
            if (file_exists($srcPath . '/install.php')) {
                require $srcPath . '/install.php';
            }

            $this->db->commit();
            
            // 4. Clear cache after success
            $this->clearCache();
            
        } catch (\Throwable $e) {
            $this->db->rollback();
            
            // Clean up files if they were copied
            if ($filesCopied and is_dir($destPath)) {
                FileSystem::delete($destPath);
            }
            
            throw $e;
        }
    }

    public function uninstall(int $id): void
    {
        $install = $this->installService->find($id);
        if (!$install) {
            return;
        }

        $root = dirname(__DIR__, 3);
        $moduleName = $install->getModuleName();
        $srcPath = $root . '/install/modules/' . $moduleName;
        $destPath = $root . '/app/Modules/' . $moduleName;

        $this->db->begin();
        try {
            // 1. Prepare context for uninstall.php
            $db = $this->db;
            $installService = $this->installService;
            $moduleFacade = $this->moduleFacade;
            $modulePermissionFacade = $this->modulePermissionFacade;
            $moduleGroupRightFacade = $this->moduleGroupRightFacade;

            // 2. Run script
            if (file_exists($srcPath . '/uninstall.php')) {
                require $srcPath . '/uninstall.php';
            }

            // 3. Delete DB record
            $this->installService->delete($id);

            // 4. Delete files
            if (is_dir($destPath)) {
                FileSystem::delete($destPath);
            }

            $this->db->commit();
            
            // 5. Clear cache after success
            $this->clearCache();
            
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Safely clears Nette cache by deleting all files while preserving the directory structure.
     * This prevents "directory not found" errors when active components (like RobotLoader)
     * try to write lock files at the end of the request.
     */
    private function clearCache(): void
    {
        $cacheDir = $this->tempDir . '/cache';
        if (is_dir($cacheDir)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($cacheDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ($files as $file) {
                if ($file->isFile()) {
                    @unlink($file->getRealPath());
                }
            }
        }
    }
}
