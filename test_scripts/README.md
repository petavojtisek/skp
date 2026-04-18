# CLI Scripts

This directory contains CLI scripts for the SKP project.

## Common Initialization
Every script should start with:
```php
/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';
```
This initializes the Nette DI container and allows you to access services like `$container->getByType(\Nette\Database\Connection::class)`.

## Existing Scripts

### `makepasswrd.php`
Generates a password hash and a random salt.
Usage: `php bin/makepasswrd.php <password>`

## Adding New Scripts
1. Create a new `.php` file in this directory.
2. Include `bootstrap.php` as shown above.
3. Use the `$container` to get necessary services.
4. Run via command line: `php bin/yourscript.php`.
