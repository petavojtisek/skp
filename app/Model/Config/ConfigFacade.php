<?php

namespace App\Model\Config;

class ConfigFacade
{
    /** @var ConfigService */
    private $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function getAllConfigs(): array
    {
        return $this->configService->getAllConfigs();
    }

    public function getConfig(int $id): ?ConfigEntity
    {
        return $this->configService->getConfig($id);
    }

    public function saveConfig(ConfigEntity $config): int
    {
        return $this->configService->saveConfig($config);
    }

    public function deleteConfig(int $id): void
    {
        $this->configService->deleteConfig($id);
    }
}
