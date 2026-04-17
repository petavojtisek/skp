<?php

namespace App\Modules\SystemConstants\Model;

use App\Model\Base\BaseService;

class SystemConstantsService extends BaseService
{
    /** @var SystemConstantsDao */
    protected $dao;

    public function __construct(SystemConstantsDao $dao)
    {
        $this->dao = $dao;
    }

    public function findSystemConstants(?string $search = null, int $limit = 10, int $offset = 0): array
    {
        return $this->dao->findSystemConstants($search, $limit, $offset);
    }

    public function countSystemConstants(?string $search = null): int
    {
        return $this->dao->countSystemConstants($search);
    }

    public function getSystemConstant(int $id): ?SystemConstantsEntity
    {
        return $this->dao->find($id);
    }

    public function saveSystemConstant(SystemConstantsEntity $entity): void
    {
        $this->dao->save($entity);
    }

    public function deleteSystemConstant(int $id): void
    {
        $this->dao->delete($id);
    }

    public function getAllSystemConstants() : array
    {
        return $this->dao->getAllSystemConstants();
    }
}
