<?php declare(strict_types=1);

echo "Path: " . dirname(__DIR__, 2) . "/install/modules\n";
echo "Dir exists: " . (is_dir(dirname(__DIR__, 2) . "/install/modules") ? 'YES' : 'NO') . "\n";
echo "Full path: " . realpath(dirname(__DIR__, 2) . "/install/modules") . "\n";

require __DIR__ . '/bootstrap.php';
use Nette\Utils\Finder;

$path = dirname(__DIR__, 2) . '/install/modules';
if (is_dir($path)) {
    foreach (Finder::findDirectories('*')->in($path) as $dir) {
        echo "Found: " . $dir->getBasename() . "\n";
    }
}
