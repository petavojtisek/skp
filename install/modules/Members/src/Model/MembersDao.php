<?php

namespace App\Modules\Members\Model;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class MembersDao extends BaseDao
{
    protected string $entityName = 'App\Modules\Members\Model\MembersEntity';

    /** @var MembersMapper */
    protected IMapper $mapper;

    public function __construct(MembersMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function findMembers(int $limit , int $offset , ?string $search = null): array
    {
        $data = $this->mapper->findMembers($limit, $offset, $search);
        return $this->getEntities($this->entityName, $data);
    }

    public function countMembers(?string $search = null): int
    {
        return $this->mapper->countMembers($search);
    }

    public function getNextMemberNumber(): int
    {
        return $this->mapper->getNextMemberNumber();
    }
}
