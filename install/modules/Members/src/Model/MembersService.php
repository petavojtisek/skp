<?php

namespace App\Modules\Members\Model;

use App\Model\Base\BaseService;

class MembersService extends BaseService
{
    private MembersDao $dao;

    public function __construct(MembersDao $dao)
    {
        $this->dao = $dao;
    }

    public function findMembers(int $limit, int $offset, ?string $search = null): array
    {
        return $this->dao->findMembers($limit, $offset, $search);
    }

    public function countMembers(?string $search = null): int
    {
        return $this->dao->countMembers($search);
    }

    public function find(int $id): ?MembersEntity
    {
        return $this->dao->find($id);
    }

    public function save(MembersEntity $entity): int
    {
        if (!$entity->getId() && !$entity->getMemberNumber()) {
            $entity->setMemberNumber($this->dao->getNextMemberNumber());
        }
        return (int)$this->dao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->dao->delete($id);
    }
}
