<?php

namespace App\Model\Config;

use App\Model\Base\BaseService;

class ConfigService extends BaseService
{
    /** @var ConfigDao */
    private $configDao;

    public function __construct(ConfigDao $configDao)
    {
        $this->configDao = $configDao;
    }

    public function getAllConfigs(): array
    {
        return $this->configDao->findAll() ?: [];
    }

    public function getConfig(int $id): ?ConfigEntity
    {
        $config = $this->configDao->find($id);

        if ($config) {

            $translates = $this->configDao->getTranslations($id);
            $config->setTranslates($translates);
        }

        return $config ?: null;
    }

    public function saveConfig(ConfigEntity $config): int
    {
        $id = (int) $this->configDao->save($config)->getId();
        return (int)$id;
    }

    public function deleteConfig(int $id): void
    {

        $this->configDao->delete($id);
    }
}
