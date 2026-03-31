<?php

namespace App\DI;

use Nette\DI\CompilerExtension;

class ModulesExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        $compiler = $this->compiler;
        $modulesDir = $this->getContainerBuilder()->parameters['appDir'] . '/Modules';

        if (is_dir($modulesDir)) {
            $files = glob($modulesDir . '/*/config.neon');
            foreach ($files as $file) {
                $compiler->loadConfig($file);
            }
        }
    }
}
