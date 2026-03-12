<?php

namespace App\Model\Config;

use App\Model\Base\BaseDao;

class ConfigDao extends BaseDao
{
    protected string $entityName = 'Config\\ConfigEntity';

    /** @var ConfigMapper */
    protected $mapper;

    public function __construct(ConfigMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getTranslations(int $configId): array
    {
        return $this->mapper->getTranslations($configId);
    }

    public function saveTranslation(int $configId, int $langId, string $value): void
    {
        $this->mapper->saveTranslation($configId, $langId, $value);
    }

    public function deleteTranslations(int $configId): void
    {
        $this->mapper->deleteTranslations($configId);
    }

    /**
     * @return ConfigMapper
     */
    public function getMapper(): \App\Model\Base\IMapper
    {
        return $this->mapper;
    }
}
