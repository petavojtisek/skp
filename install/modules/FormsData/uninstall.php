<?php
/**
 * Uninstall script for FormsData module
 */

$manifest = json_decode(file_get_contents(__DIR__ . '/manifest.json'), true);
$codeName = $manifest['code_name'];

$moduleId = $db->select('module_id')->from('module')->where('module_code_name = %s', $codeName)->fetchSingle();

if ($moduleId) {
    // 1. Remove rights
    $db->query("DELETE FROM `module_group_right` WHERE `module_id` = %i", $moduleId);
    $db->query("DELETE FROM `module_right` WHERE `module_id` = %i", $moduleId);

    // 2. Remove module
    $db->query("DELETE FROM `module` WHERE `module_id` = %i", $moduleId);
}

// 3. Remove install record
$db->query("DELETE FROM `install` WHERE `module_name` = %s", $manifest['name']);

// 4. Database cleanup
$sql = file_get_contents(__DIR__ . '/uninstall.sql');
foreach (explode(';', $sql) as $query) {
    $query = trim($query);
    if ($query) {
        $db->query($query);
    }
}
