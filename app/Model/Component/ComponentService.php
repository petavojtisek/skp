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
        $this->db->query("REPLACE INTO `page_component` (`page_id`, `component_id`) VALUES (%i, %i)", $pageId, $componentId);
    }

    public function unlinkFromPage(int $componentId, int $pageId): void
    {
        $this->db->query("DELETE FROM `page_component` WHERE `page_id` = %i AND `component_id` = %i", $pageId, $componentId);
    }

    public function save(ComponentEntity $entity): int
    {
        return (int)$this->componentDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->db->begin();
        try {
            $this->db->query("DELETE FROM `page_component` WHERE `component_id` = %i", $id);
            $this->componentDao->delete($id);
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
