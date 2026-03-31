<?php

namespace App\Model\Component;

class ComponentFacade
{
    private ComponentService $componentService;

    public function __construct(ComponentService $componentService)
    {
        $this->componentService = $componentService;
    }

    public function getByPageId(int $pageId): array
    {
        return $this->componentService->getByPageId($pageId);
    }

    public function find(int $id): ?ComponentEntity
    {
        return $this->componentService->find($id);
    }

    public function getExistingNotOnPage(int $pageId, int $templateId): array
    {
        return $this->componentService->getExistingNotOnPage($pageId, $templateId);
    }

    public function linkToPage(int $componentId, int $pageId): void
    {
        $this->componentService->linkToPage($componentId, $pageId);
    }

    public function unlinkFromPage(int $componentId, int $pageId): void
    {
        $this->componentService->unlinkFromPage($componentId, $pageId);
    }

    public function save(ComponentEntity $entity): int
    {
        return $this->componentService->save($entity);
    }

    public function delete(int $id): void
    {
        $this->componentService->delete($id);
    }
}
