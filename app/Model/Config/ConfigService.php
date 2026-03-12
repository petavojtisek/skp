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
            $config->setTranslations($this->configDao->getMapper()->getTranslations($id));
        }
        return $config ?: null;
    }

    public function saveConfig(ConfigEntity $config): int
    {
        $id = $config->getId();
        if ($id) {
            $this->configDao->update($config);
        } else {
            $id = (int) $this->configDao->insert($config);
        }

        // Save translations (excluding default if logic requires it, but here we save all provided)
        foreach ($config->getTranslations() as $langId => $value) {
            if ($langId == C_LANGUAGE_CS) continue; // CS is already in the main table value
            if ($value === null || $value === '') continue; // Skip empty translations
            
            $this->configDao->getMapper()->saveTranslation((int)$id, (int)$langId, (string)$value);
        }

        return (int)$id;
    }

    public function deleteConfig(int $id): void
    {
        $this->configDao->getMapper()->deleteTranslations($id);
        $this->configDao->delete($id);
    }
}
