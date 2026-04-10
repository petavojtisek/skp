<?php

namespace App\Modules\ContentVersion\Model;

class ContentVersionFacade
{
    private ContentVersionService $service;

    public function __construct(ContentVersionService $service)
    {
        $this->service = $service;
    }

    public function getByComponentId(int $componentId): array
    {
        return $this->service->getByComponentId($componentId);
    }

    public function find(int $id): ?ContentVersionEntity
    {
        return $this->service->find($id);
    }

    public function save(ContentVersionEntity $entity): int
    {
        return $this->service->save($entity);
    }

    public function delete(int $id): void
    {
        $this->service->delete($id);
    }

}
