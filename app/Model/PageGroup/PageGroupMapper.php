<?php

namespace App\Model\PageGroup;

use App\Model\Base\BaseMapper;

class PageGroupMapper extends BaseMapper
{
    protected string $tableName = 'page_group';
    protected string $primaryKey = 'id';

    // --- Vazba: Skupina stránek <-> Administrátorská skupina (globální práva k celým skupinám) ---

    public function toggleAdminGroup(int $pageGroupId, int $adminGroupId, bool $state): void
    {
        if ($state) {
            $exists = $this->db->fetch('SELECT 1 FROM page_group_admin_group WHERE page_group_id = %i AND admin_group_id = %i', $pageGroupId, $adminGroupId);
            if (!$exists) {
                $this->db->query('INSERT INTO page_group_admin_group', [
                    'page_group_id' => $pageGroupId,
                    'admin_group_id' => $adminGroupId
                ]);
            }
        } else {
            $this->db->query('DELETE FROM page_group_admin_group WHERE page_group_id = %i AND admin_group_id = %i', $pageGroupId, $adminGroupId);
        }
    }

    public function getAdminGroupIds(int $pageGroupId): array
    {
        return $this->db->select('admin_group_id')
            ->from('page_group_admin_group')
            ->where('page_group_id = %i', $pageGroupId)
            ->fetchPairs('admin_group_id', 'admin_group_id');
    }

    public function getAdminGroupIdsByPageGroups(array $pageGroupIds): array
    {
        if (empty($pageGroupIds)) return [];
        return $this->db->select('admin_group_id')
            ->from('page_group_admin_group')
            ->where('page_group_id IN (%i)', $pageGroupIds)
            ->fetchPairs('admin_group_id', 'admin_group_id');
    }

    // --- Vazba: Stránka <-> Skupina stránek (page_in_group - pro administraci) ---

    public function togglePageInGroup(int $pageId, int $pageGroupId, bool $state): void
    {
        if ($state) {
            $exists = $this->db->fetch('SELECT 1 FROM page_in_group WHERE page_id = %i AND page_group_id = %i', $pageId, $pageGroupId);
            if (!$exists) {
                $this->db->query('INSERT INTO page_in_group', [
                    'page_id' => $pageId,
                    'page_group_id' => $pageGroupId
                ]);
            }
        } else {
            $this->db->query('DELETE FROM page_in_group WHERE page_id = %i AND page_group_id = %i', $pageId, $pageGroupId);
        }
    }

    public function getPageInGroupIds(int $pageId): array
    {
        return $this->db->select('page_group_id')
            ->from('page_in_group')
            ->where('page_id = %i', $pageId)
            ->fetchPairs('page_group_id', 'page_group_id');
    }

    // --- Vazba: Stránka <-> Uživatelská skupina (page_in_group_user - pro frontend) ---

    public function togglePageInGroupUser(int $pageId, int $pageGroupId, bool $state): void
    {
        if ($state) {
            $exists = $this->db->fetch('SELECT 1 FROM page_in_group_user WHERE page_id = %i AND page_group_id = %i', $pageId, $pageGroupId);
            if (!$exists) {
                $this->db->query('INSERT INTO page_in_group_user', [
                    'page_id' => $pageId,
                    'page_group_id' => $pageGroupId
                ]);
            }
        } else {
            $this->db->query('DELETE FROM page_in_group_user WHERE page_id = %i AND page_group_id = %i', $pageId, $pageGroupId);
        }
    }

    public function getPageInGroupUserIds(int $pageId): array
    {
        return $this->db->select('page_group_id')
            ->from('page_in_group_user')
            ->where('page_id = %i', $pageId)
            ->fetchPairs('page_group_id', 'page_group_id');
    }

    // --- Pomocné metody pro číselníky ---

    public function getAccessiblePageGroupNames(int $adminGroupId): array
    {
        return $this->db->select('pg.name, 1 as val')
            ->from($this->tableName)->as('pg')
            ->join('page_group_admin_group')->as('pgag')->on('pg.id = pgag.page_group_id')
            ->where('pgag.admin_group_id = %i', $adminGroupId)
            ->fetchPairs('name', 'val');
    }

    public function getAccessiblePageGroupIdsWithNames(int $adminGroupId): array
    {
        return $this->db->select('pg.id, pg.name')
            ->from($this->tableName)->as('pg')
            ->join('page_group_admin_group')->as('pgag')->on('pg.id = pgag.page_group_id')
            ->where('pgag.admin_group_id = %i', $adminGroupId)
            ->fetchPairs('id', 'name');
    }

    public function getPageGroupsByPageId(int $pageId): array
    {
        return $this->db->select('pg.*')
            ->from($this->tableName)->as('pg')
            ->join('page_in_group')->as('pig')->on('pg.id = pig.page_group_id')
            ->where('pig.page_id = %i', $pageId)
            ->fetchAssoc('id');
    }
}
