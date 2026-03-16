<?php

namespace App\Model\PageGroup;

use App\Model\Base\BaseMapper;

class PageGroupMapper extends BaseMapper
{
    protected string $tableName = 'page_group';
    protected string $primaryKey = 'id';

    public function toggleAdminGroup(int $pageGroupId, int $adminGroupId, bool $state): void
    {
        if ($state) {
            $exists = $this->db->fetch('SELECT 1 FROM page_group_admin_group WHERE page_group_id = ? AND admin_group_id = ?', $pageGroupId, $adminGroupId);
            if (!$exists) {
                $this->db->query('INSERT INTO page_group_admin_group', [
                    'page_group_id' => $pageGroupId,
                    'admin_group_id' => $adminGroupId
                ]);
            }
        } else {
            $this->db->query('DELETE FROM page_group_admin_group WHERE page_group_id = ? AND admin_group_id = ?', $pageGroupId, $adminGroupId);
        }
    }

    public function getAdminGroupIds(int $pageGroupId): array
    {
        return $this->db->select('admin_group_id')
            ->from('page_group_admin_group')
            ->where('page_group_id = ?', $pageGroupId)
            ->fetchPairs('admin_group_id', 'admin_group_id');
    }

    public function getAccessiblePageGroupIds(int $adminGroupId): array
    {
        return $this->db->select('page_group_id, 1 as val')
            ->from('page_group_admin_group')
            ->where('admin_group_id = ?', $adminGroupId)
            ->fetchPairs('page_group_id', 'val');
    }

    public function getAccessiblePageGroupNames(int $adminGroupId): array
    {
        return $this->db->select('pg.name, 1 as val')
            ->from($this->tableName)->as('pg')
            ->join('page_group_admin_group')->as('pgag')->on('pg.id = pgag.page_group_id')
            ->where('pgag.admin_group_id = ?', $adminGroupId)
            ->fetchPairs('name', 'val');
    }
}
