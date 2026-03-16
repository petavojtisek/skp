<?php

namespace App\Model\AdminGroup;

use App\Model\Base\BaseService;

class AdminGroupService extends BaseService
{
    private AdminGroupDao $adminGroupDao;

    public function __construct(AdminGroupDao $adminGroupDao)
    {
        $this->adminGroupDao = $adminGroupDao;
    }

    public function findAll(): array
    {
        return $this->adminGroupDao->findAll();
    }

    public function find(int $id): ?AdminGroupEntity
    {
        return $this->adminGroupDao->find($id);
    }

    public function save(AdminGroupEntity $entity): int
    {
        return (int)$this->adminGroupDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->adminGroupDao->delete($id);
    }

    public function getAdminGroups(): array
    {
        return $this->adminGroupDao->getAdminGroups();
    }

    public function getAdminInGroups(int $adminId): array
    {
        return $this->adminGroupDao->getAdminInGroups($adminId);
    }

    public function saveAdminGroups(int $adminId, array $groupIds): void
    {
        $this->adminGroupDao->saveAdminGroups($adminId, $groupIds);
    }

    /**
     * Returns all groups available to a user starting from their own group and its descendants.
     */
    public function getAvailableGroups(int $startGroupId): array
    {
        $allGroups = $this->findAll();
        $availableIds = $this->getAvailableGroupIds($startGroupId, $allGroups);
        
        $availableGroups = [];
        foreach ($allGroups as $group) {
            if (in_array($group->admin_group_id, $availableIds)) {
                $availableGroups[] = $group;
            }
        }
        return $availableGroups;
    }

    public function getAvailableGroupIds(int $startGroupId, ?array $allGroups = null): array
    {
        if ($allGroups === null) {
            $allGroups = $this->findAll();
        }

        $ids = [$startGroupId];
        foreach ($allGroups as $group) {
            if ($group->pid == $startGroupId) {
                $ids = array_merge($ids, $this->getAvailableGroupIds($group->admin_group_id, $allGroups));
            }
        }
        return array_unique($ids);
    }

    /**
     * @return array
     */
    public function getGroupTree(): array
    {
        $groups = $this->findAll();
        return $this->buildTree($groups, 0);
    }

    /**
     * @param array $elements
     * @param int $parentId
     * @param int $level
     * @return array
     */
    private function buildTree(array $elements, int $parentId = 0, int $level = 1): array
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element->pid == $parentId) {
                $children = $this->buildTree($elements, $element->admin_group_id, $level + 1);
                $branch[] = [
                    'entity' => $element,
                    'level' => $level,
                    'items' => $children
                ];
            }
        }

        return $branch;
    }
}
