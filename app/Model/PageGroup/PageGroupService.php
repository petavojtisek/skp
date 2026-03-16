<?php

namespace App\Model\PageGroup;

use App\Model\Base\BaseService;

class PageGroupService extends BaseService
{
    /** @var PageGroupDao */
    private $pageGroupDao;

    public function __construct(PageGroupDao $pageGroupDao)
    {
        $this->pageGroupDao = $pageGroupDao;
    }

    public function findAll(): array
    {
        return $this->pageGroupDao->findAll() ?: [];
    }

    public function find(int $id): ?PageGroupEntity
    {
        return $this->pageGroupDao->find($id) ?: null;
    }

    public function save(PageGroupEntity $entity): int
    {
        return (int)$this->pageGroupDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->pageGroupDao->delete($id);
    }
}
