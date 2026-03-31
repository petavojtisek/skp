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
}
