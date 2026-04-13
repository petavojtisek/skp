<?php
/**
 * Uninstall script for News module
 */

// 1. Get IDs for cleanup
$manifest = json_decode(file_get_contents(__DIR__ . '/manifest.json'), true);
$name = $manifest['name'];

$moduleId = $db->select('module_id')->from('module')->where('module_name = %s', $name)->fetchSingle();

if ($moduleId) {
    // 2. Remove rights and group rights
    $db->delete('module_group_right')->where('module_id = %i', $moduleId)->execute();
    $db->delete('module_right')->where('module_id = %i', $moduleId)->execute();
    
    // 3. Remove from module table
    $db->delete('module')->where('module_id = %i', $moduleId)->execute();
}

// 4. Run SQL cleanup
$sql = file_get_contents(__DIR__ . '/uninstall.sql');
foreach (explode(';', $sql) as $query) {
    $query = trim($query);
    if ($query) {
        $db->query($query);
    }
}
