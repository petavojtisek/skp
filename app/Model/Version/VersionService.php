<?php

namespace App\Model\Version;

use App\Model\Base\BaseService;

class VersionService extends BaseService
{
    private VersionDao $versionDao;

    public function __construct(VersionDao $versionDao)
    {
        $this->versionDao = $versionDao;
    }

    public function setActiveVersion(int $componentId, int $elementId): void
    {
        $this->versionDao->setActiveVersion($componentId, $elementId);
    }

    public function getActiveElementId(int $componentId): ?int
    {
        return $this->versionDao->getActiveElementId($componentId);
    }
}
