<?php

namespace App\Model\Element;

class ElementFacade
{
    private ElementService $elementService;

    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    public function find(int $id): ?ElementEntity
    {
        return $this->elementService->find($id);
    }

    public function save(ElementEntity $entity): int
    {
        return $this->elementService->save($entity);
    }

    public function delete(int $id): void
    {
        $this->elementService->delete($id);
    }
}
