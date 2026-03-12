<?php declare(strict_types=1);

/**
 * CLI Script for generating password hash and salt.
 * Usage: php makepasswrd.php <password>
 */

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';

use Nette\Security\Passwords;

if (!isset($argv[1])) {
    echo "Usage: php " . basename($argv[0]) . " <password>\n";
    exit(1);
}

$password = $argv[1];

// Get Passwords service from DI container
$passwords = $container->getByType(Passwords::class);
$hash = $passwords->hash($password);

/**
 * Generate a separate random salt.
 * 
 * Note: Modern Nette (and PHP's password_hash) includes a cryptographically secure salt 
 * directly within the hash string (e.g., $2y$10$SALT...HASH...). 
 * If your database has a separate 'salt' column, you can store this generated value there, 
 * but it's usually not needed for verification when using Nette\Security\Passwords.
 */
$salt = bin2hex(random_bytes(16));

echo "------------------------------------------------------------\n";
echo "Password: " . $password . "\n";
echo "Hash:     " . $hash . "\n";
echo "Salt:     " . $salt . "\n";
echo "------------------------------------------------------------\n";
echo "INFO: In Nette 3.x, the hash string already includes the salt.\n";
echo "------------------------------------------------------------\n";
