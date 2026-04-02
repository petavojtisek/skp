<?php

namespace App\Model\Version;

class VersionFacade
{
    private VersionService $versionService;

    public function __construct(VersionService $versionService)
    {
        $this->versionService = $versionService;
    }

    public function setActiveVersion(int $componentId, int $elementId): void
    {
        $this->versionService->setActiveVersion($componentId, $elementId);
    }

    public function getActiveElementId(int $componentId): ?int
    {
        return $this->versionService->getActiveElementId($componentId);
    }
}
