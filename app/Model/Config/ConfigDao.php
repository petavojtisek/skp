<?php

namespace App\Model\Config;

use App\Model\Base\BaseDao;

class ConfigDao extends BaseDao
{
    protected $entityName = 'Config\ConfigEntity';

    public function __construct(ConfigMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getDescriptions(int $configId): array
    {
        return $this->mapper->getDescriptions($configId);
    }

    public function saveDescription(int $configId, int $langId, string $description): void
    {
        $this->mapper->saveDescription($configId, $langId, $description);
    }

    public function deleteDescriptions(int $configId): void
    {
        $this->mapper->deleteDescriptions($configId);
    }
}
