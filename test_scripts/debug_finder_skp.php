<?php declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
use Nette\Utils\Finder;

$path = dirname(__DIR__, 1) . '/install/modules';
echo "Checking path: $path\n";

if (is_dir($path)) {
    foreach (Finder::findDirectories('*')->in($path) as $dir) {
        echo "Found: " . $dir->getBasename() . "\n";
    }
} else {
    echo "Directory does not exist!\n";
}
