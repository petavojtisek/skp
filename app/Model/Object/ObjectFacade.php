<?php

namespace App\Model\Object;

class ObjectFacade
{
    /** @var ObjectService */
    private $objectService;

    public function __construct(ObjectService $objectService)
    {
        $this->objectService = $objectService;
    }

    public function getObjects(): array
    {
        return $this->objectService->findAll();
    }

    public function getObject(int $id): ?ObjectEntity
    {
        return $this->objectService->find($id);
    }

    public function saveObject(ObjectEntity $entity): int
    {
        return $this->objectService->save($entity);
    }

    public function deleteObject(int $id): void
    {
        $this->objectService->delete($id);
    }
}
