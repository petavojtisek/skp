<?php
/**
 * Uninstall script for SystemConstants module
 */

$manifest = json_decode(file_get_contents(__DIR__ . '/manifest.json'), true);
$codeName = $manifest['code_name'];

$moduleId = $db->select('module_id')->from('module')->where('module_code_name = %s', $codeName)->fetchSingle();

if ($moduleId) {
    $db->query("DELETE FROM `module_group_right` WHERE `module_id` = %i", $moduleId);
    $db->query("DELETE FROM `module_right` WHERE `module_id` = %i", $moduleId);
    $db->query("DELETE FROM `module` WHERE `module_id` = %i", $moduleId);
}

$db->query("DELETE FROM `install` WHERE `module_name` = %s", $manifest['name']);

$sql = file_get_contents(__DIR__ . '/uninstall.sql');
foreach (explode(';', $sql) as $query) {
    $query = trim($query);
    if ($query) {
        $db->query($query);
    }
}
