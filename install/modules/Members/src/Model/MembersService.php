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

    public function findMembers(int $limit, int $offset, ?string $search = null, ?string $source = null, ?bool $registrationEmail = null, ?bool $registrationConfirm = null, ?bool $paymentConfirm = null, ?bool $isPaid = null, ?bool $active = null): array
    {
        return $this->dao->findMembers($limit, $offset, $search, $source, $registrationEmail, $registrationConfirm, $paymentConfirm, $isPaid, $active);
    }

    public function countMembers(?string $search = null, ?string $source = null, ?bool $registrationEmail = null, ?bool $registrationConfirm = null, ?bool $paymentConfirm = null, ?bool $isPaid = null, ?bool $active = null): int
    {
        return $this->dao->countMembers($search, $source, $registrationEmail, $registrationConfirm, $paymentConfirm, $isPaid, $active);
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
