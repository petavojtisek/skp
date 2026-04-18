<?php

namespace App\Modules\News\Model;

class NewsFacade
{
    private NewsService $service;

    public function __construct(NewsService $service)
    {
        $this->service = $service;
    }

    public function getByComponentId(int $componentId): array
    {
        return $this->service->getByComponentId($componentId);
    }

    public function getFrontByComponentId(int $componentId): array
    {
        return $this->service->getFrontByComponentId($componentId);
    }

    public function find(?int $id): ?NewsEntity
    {
        if(!$id){
            return null;
        }

        return $this->service->find($id);
    }

    public function save(NewsEntity $entity): int
    {
        return $this->service->save($entity);
    }

    public function delete(int $id): void
    {
        $this->service->delete($id);
    }
}
