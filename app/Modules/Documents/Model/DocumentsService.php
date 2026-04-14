<?php

namespace App\Modules\Documents\Model;

use App\Model\Base\BaseService;

class DocumentsService extends BaseService
{
    private DocumentsDao $dao;

    public function __construct(DocumentsDao $dao)
    {
        $this->dao = $dao;
    }

    public function getByComponentId(int $componentId): array
    {
        return $this->dao->getByComponentId($componentId);
    }

    public function getFrontByComponentId(int $componentId): array
    {
        return $this->dao->getFrontByComponentId($componentId);
    }

    public function find(int $id): ?DocumentsEntity
    {
        return $this->dao->find($id);
    }

    public function save(DocumentsEntity $entity): int
    {
        return (int)$this->dao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->dao->delete($id);
    }
}
