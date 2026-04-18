<?php declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';

use Dibi\Connection;

$db = $container->getByType(Connection::class);

$pageName = 'testddd';
$page = $db->select('*')->from('page')->where('page_name = %s', $pageName)->fetch();

if (!$page) {
    echo "Page '$pageName' not found.\n";
    exit;
}

$pageId = (int)$page->page_id;
echo "Page: $pageName (ID: $pageId)\n";

// Page groups (page_in_group)
$pageGroupIds = $db->select('page_group_id')
    ->from('page_in_group')
    ->where('page_id = %i', $pageId)
    ->fetchPairs(null, 'page_group_id');

echo "Associated Page Group IDs: " . implode(', ', $pageGroupIds) . "\n";

if (!empty($pageGroupIds)) {
    // Admin groups with explicit access (page_group_admin_group)
    $adminGroups = $db->select('admin_group_id')
        ->from('page_group_admin_group')
        ->where('page_group_id IN (%i)', $pageGroupIds)
        ->fetchPairs(null, 'admin_group_id');

    echo "Admin Group IDs with access via page_group_admin_group:\n";
    print_r($adminGroups);
} else {
    echo "Page is not assigned to any group.\n";
}
