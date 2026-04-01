<?php

namespace App\Model\Component;

use App\Model\Base\BaseService;

class ComponentService extends BaseService
{
    private ComponentDao $componentDao;

    public function __construct(ComponentDao $componentDao)
    {
        $this->componentDao = $componentDao;
    }

    public function find(int $id): ?ComponentEntity
    {
        return $this->componentDao->find($id) ?: null;
    }

    public function findWithModule(int $id): ?ComponentEntity
    {
        return $this->componentDao->findWithModule($id);
    }

    public function getByPageId(int $pageId): array
    {
        return $this->componentDao->getByPageId($pageId);
    }

    public function getExistingNotOnPage(int $pageId, int $templateId): array
    {
        return $this->componentDao->getExistingNotOnPage($pageId, $templateId);
    }

    public function linkToPage(int $componentId, int $pageId): void
    {
        $this->componentDao->linkToPage($componentId, $pageId);
    }

    public function unlinkFromPage(int $componentId, int $pageId): void
    {
        $this->componentDao->unlinkFromPage($componentId, $pageId);
    }

    public function save(ComponentEntity $entity): int
    {
        return (int)$this->componentDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        // Delete links first
        $this->componentDao->deleteLinks($id);
        // Then delete the component itself
        $this->componentDao->delete($id);
    }
}
