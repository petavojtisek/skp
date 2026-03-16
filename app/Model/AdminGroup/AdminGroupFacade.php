<?php

namespace App\Model\AdminGroup;

class AdminGroupFacade
{
    /** @var AdminGroupService */
    private $adminGroupService;

    public function __construct(AdminGroupService $adminGroupService)
    {
        $this->adminGroupService = $adminGroupService;
    }

    public function getGroups(): array
    {
        return $this->adminGroupService->findAll();
    }

    public function getGroup(int $id): ?AdminGroupEntity
    {
        return $this->adminGroupService->find($id);
    }

    public function saveGroup(AdminGroupEntity $entity): int
    {
        return $this->adminGroupService->save($entity);
    }

    public function deleteGroup(int $id): void
    {
        $this->adminGroupService->delete($id);
    }

    public function getGroupTree(): array
    {
        return $this->adminGroupService->getGroupTree();
    }
}
