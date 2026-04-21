<?php

namespace App\Modules\News\Model;

use App\Model\Base\BaseService;

class NewsService extends BaseService
{
    private NewsDao $dao;

    public function __construct(NewsDao $dao)
    {
        $this->dao = $dao;
    }

    public function getByComponentId(int $componentId): array
    {
        return $this->dao->getByComponentId($componentId);
    }

    public function getFrontByComponentId(int $componentId,?int $limit = null, ?int $offset=null): array
    {
        return $this->dao->getFrontByComponentId($componentId, $limit, $offset);
    }

    public function find(int $id): ?NewsEntity
    {
        return $this->dao->find($id);
    }

    public function save(NewsEntity $entity): int
    {
        return (int)$this->dao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->dao->delete($id);
    }
}
