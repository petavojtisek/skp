<?php

namespace App\Modules\ContentVersion\Model;

use App\Model\Base\BaseService;

class ContentVersionService extends BaseService
{
    private ContentVersionDao $dao;

    public function __construct(ContentVersionDao $dao)
    {
        $this->dao = $dao;
    }

    public function getByComponentId(int $componentId): array
    {
        return $this->dao->getByComponentId($componentId);
    }

    public function find(int $id): ?ContentVersionEntity
    {
        return $this->dao->find($id);
    }

    public function save(ContentVersionEntity $entity): int
    {
        return (int)$this->dao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->dao->delete($id);
    }
}
