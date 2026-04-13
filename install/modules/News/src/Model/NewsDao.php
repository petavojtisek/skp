<?php

namespace App\Modules\News\Model;

use App\Model\Base\BaseDao;
use App\Model\Base\IEntity;
use App\Model\Base\IMapper;

class NewsDao extends BaseDao
{
    protected string $entityName = 'App\Modules\News\Model\NewsEntity';

    /** @var NewsMapper */
    protected IMapper $mapper;

    public function __construct(NewsMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getByComponentId(int $componentId): array
    {
        $data = $this->mapper->getByComponentId($componentId);
        return $this->getEntities($this->entityName, $data);
    }

    public function getFrontByComponentId(int $componentId): array
    {
        $data = $this->mapper->getFrontByComponentId($componentId);
        return $this->getEntities($this->entityName, $data);
    }

    public function getEntity(string $entityName, array $data = [], ?string $lang = null): ?IEntity
    {
        $entity = null;
        try {
            $entity = new NewsEntity($data);
        } catch (\Exception $e) {
        }
        return $entity;
    }
}
