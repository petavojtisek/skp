<?php

namespace App\Model\Version;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class VersionDao extends BaseDao
{
    protected string $entityName = 'Version\\VersionEntity';

    /** @var VersionMapper */
    protected IMapper $mapper;

    public function __construct(VersionMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function setActiveVersion(int $componentId, int $elementId): void
    {
        $this->mapper->setActiveVersion($componentId, $elementId);
    }

    public function getActiveElementId(int $componentId): ?int
    {
        return $this->mapper->getActiveElementId($componentId);
    }
}
