<?php

namespace App\Model\AdminGroupRight;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class AdminGroupRightDao extends BaseDao
{
    protected string $entityName = 'AdminGroupRight\AdminGroupRightEntity';

    /** @var AdminGroupRightMapper */
    protected IMapper $mapper;

    public function __construct(AdminGroupRightMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }

    public function toggleRight(int $groupId, int $rightId, bool $state): void
    {
        $this->mapper->toggleRight($groupId, $rightId, $state);
    }

    public function getGroupRightsIds(int $groupId): array
    {
        return $this->mapper->getGroupRightsIds($groupId);
    }

    public function getGroupRightsCodes(int $groupId): array
    {
        return $this->mapper->getGroupRightsCodes($groupId);
    }
}
