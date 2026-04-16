<?php

namespace App\Modules\Forms\Model;

use App\Model\Base\BaseService;

class FormsService extends BaseService
{
    /** @var FormsDao */
    protected $dao;

    public function __construct(FormsDao $dao)
    {
        $this->dao = $dao;
    }

    public function findForms(int $limit, int $offset, ?string $search = null): array
    {
        return $this->dao->findForms($limit, $offset, $search);
    }

    public function countForms(?string $search = null): int
    {
        return $this->dao->countForms($search);
    }

    public function getForm(int $id): ?FormsEntity
    {
        return $this->dao->findById($id);
    }

    public function deleteForm(int $id): void
    {
        $this->dao->delete($id);
    }

    public function saveForm(FormsEntity $entity): void
    {
        $this->dao->save($entity);
    }
}
