<?php

namespace App\Modules\FormsData\Model;

use App\Model\Base\BaseService;

class FormsDataService extends BaseService
{
    /** @var FormsDataDao */
    protected $dao;

    public function __construct(FormsDataDao $dao)
    {
        $this->dao = $dao;
    }

    public function findFormsData(int $limit, int $offset, ?string $search = null): array
    {
        return $this->dao->findFormsData($limit, $offset, $search);
    }

    public function countFormsData(?string $search = null): int
    {
        return $this->dao->countFormsData($search);
    }

    public function getFormData(int $id): ?FormsDataEntity
    {
        return $this->dao->find($id);
    }

    public function deleteFormData(int $id): void
    {
        $this->dao->delete($id);
    }

    public function saveFormData(FormsDataEntity $entity): void
    {
        $this->dao->save($entity);
    }
}

