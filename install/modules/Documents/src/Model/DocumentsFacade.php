<?php

namespace App\Modules\Documents\Model;

class DocumentsFacade
{
    private DocumentsService $service;

    public function __construct(DocumentsService $service)
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

    public function find(int $id): ?DocumentsEntity
    {
        return $this->service->find($id);
    }

    public function save(DocumentsEntity $entity): int
    {
        return $this->service->save($entity);
    }

    public function delete(int $id): void
    {
        $this->service->delete($id);
    }
}
