<?php

namespace App\Model\PageGroup;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class PageGroupDao extends BaseDao
{
    protected string $entityName = 'PageGroup\PageGroupEntity';

    /** @var PageGroupMapper */
    protected IMapper $mapper;

    public function __construct(PageGroupMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }

    public function toggleAdminGroup(int $pageGroupId, int $adminGroupId, bool $state): void
    {
        $this->mapper->toggleAdminGroup($pageGroupId, $adminGroupId, $state);
    }

    public function getAdminGroupIds(int $pageGroupId): array
    {
        return $this->mapper->getAdminGroupIds($pageGroupId);
    }
}
