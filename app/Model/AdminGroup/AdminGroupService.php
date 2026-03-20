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

    /**
     * @return AdminGroupEntity[]
     */
    public function findAll(): array
    {
        return $this->adminGroupDao->findAll() ?: [];
    }

    public function find(int $id): ?AdminGroupEntity
    {
        return $this->adminGroupDao->find($id) ?: null;
    }

    public function save(AdminGroupEntity $entity): int
    {
        return (int)$this->adminGroupDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->adminGroupDao->delete($id);
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
     * @return AdminGroupEntity[]
     */
    public function getAvailableGroups(int $startGroupId): array
    {
        $allGroups = $this->findAll();
        $availableIds = $this->getAvailableGroupIds($startGroupId, $allGroups);

        $availableGroups = [];
        foreach ($allGroups as $group) {
            $id = (int)$group->getId();
            if (in_array($id, $availableIds)) {
                $availableGroups[$id] = $group;
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
            if ((int)$group->pid === $startGroupId) {
                $ids = array_merge($ids, $this->getAvailableGroupIds((int)$group->getId(), $allGroups));
            }
        }
        return array_unique($ids);
    }

    /**
     * @param int $parentId
     * @return array
     */
    public function getGroupSubtree(int $parentId): array
    {
        $groups = $this->findAll();
        return $this->buildTree($groups, $parentId);
    }

    /**
     * @return array
     */
    public function getGroupTree(int $startId = 0): array
    {
        $groups = $this->findAll();

        if ($startId > 0) {
            $root = $this->find($startId);
            if (!$root) {
                return [];
            }

            return [
                [
                    'entity' => $root,
                    'level' => 1,
                    'items' => $this->buildTree($groups, $startId, 2)
                ]
            ];
        }

        return $this->buildTree($groups, 0);
    }

    /**
     * @param AdminGroupEntity[] $elements
     * @param int $parentId
     * @param int $level
     * @return array
     */
    private function buildTree(array $elements, int $parentId = 0, int $level = 1): array
    {
        $branch = [];

        foreach ($elements as $element) {
            if ((int)$element->pid === $parentId) {
                $children = $this->buildTree($elements, (int)$element->getId(), $level + 1);
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
