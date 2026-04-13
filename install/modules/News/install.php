<?php
/**
 * Install script for News module
 */
use App\Model\Install\InstallEntity;
use App\Model\Module\ModuleEntity;
use App\Model\ModulePermission\ModulePermissionEntity;

// 1. Load manifest
$manifest = json_decode(file_get_contents(__DIR__ . '/manifest.json'), true);
$name = $manifest['name'];
$codeName = $manifest['code_name'];
$className = $manifest['class_name'];
$type = $manifest['type'];

// 2. Database setup (tables)
$sql = file_get_contents(__DIR__ . '/install.sql');
foreach (explode(';', $sql) as $query) {
    $query = trim($query);
    if ($query) {
        $db->query($query);
    }
}

// 3. Register in 'install' table
$install = new InstallEntity();
$install->setModuleName($name);
$install->setInstalled(1);
$install->setPath("Modules/$name");
$install = $installService->save($install);
$installId = $install->getId();

// 4. Register in 'module' table
$module = new ModuleEntity();
$module->setInstallId($installId);
$module->setModuleType($type);
$module->setModuleActive('Y');
$module->setModuleName($name);
$module->setModuleCodeName($codeName);
$module->setModuleClassName($className);
$moduleId = $moduleFacade->save($module); // Returns int

// 5. Register Permissions & Rights
$rights = [
    'list' => 'Zobrazení',
    'insert' => 'Přidání',
    'edit' => 'Editace',
    'delete' => 'Mazání',
];

foreach ($rights as $code => $rightName) {
    // Check if permission exists
    $permId = $db->select('permission_id')->from('module_permission')->where('right_code_name = %s', $code)->fetchSingle();

    if (!$permId) {
        $perm = new ModulePermissionEntity();
        $perm->setRightCodeName($code);
        $perm->setName($rightName);
        $permId = $modulePermissionFacade->save($perm); // returns int
    }

    // Link right to module
    $db->query("REPLACE INTO `module_right` (`module_id`, `permission_id`) VALUES (%i, %i)", $moduleId, $permId);

    // Grant to SuperAdmin (group 1)
    $db->query("REPLACE INTO `module_group_right` (`admin_group_id`, `module_id`, `permission_id`) VALUES (1, %i, %i)", $moduleId, $permId);
}
