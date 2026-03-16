<?php

namespace App\Model\Admin;

use App\Model\Base\BaseMapper;

class AdminMapper extends BaseMapper
{
    protected string $tableName = 'admin';
    protected string $primaryKey = 'admin_id';

    public function getActiveAdmins(): array
    {
        return $this->db->select('a.*, g.admin_group_name')
            ->from($this->tableName)->as('a')
            ->leftJoin('admin_group')->as('g')->on('a.admin_group_id = g.admin_group_id')
            ->where('a.status != %i', constant('C_ADMINISTRATOR_STATUS_REMOVED'))
            ->fetchAll();
    }

    public function getAdminGroups(): array
    {
        $rows = $this->db->select('*')->from('admin_group')->fetchAssoc('admin_group_id');
        return array_map(fn($row) => (array)$row, $rows);
    }

    public function getAdminInGroups(int $adminId): array
    {
        return $this->db->select('group_id')
            ->from('admin_in_group')
            ->where('admin_id = %i', $adminId)
            ->fetchPairs(null, 'group_id');
    }

    public function saveAdminGroups(int $adminId, array $groupIds): void
    {
        $this->db->delete('admin_in_group')->where('admin_id = %i', $adminId)->execute();
        foreach ($groupIds as $groupId) {
            $this->db->insert('admin_in_group', [
                'admin_id' => $adminId,
                'group_id' => $groupId
            ])->execute();
        }
    }

    public function getAdminPresentations(int $adminId): array
    {
        return $this->db->select('presentation_id')
            ->from('admin_presentation')
            ->where('admin_id = %i', $adminId)
            ->fetchPairs(null, 'presentation_id');
    }

    public function saveAdminPresentations(int $adminId, array $presentationIds): void
    {
        $this->db->delete('admin_presentation')->where('admin_id = %i', $adminId)->execute();
        foreach ($presentationIds as $presentationId) {
            $this->db->insert('admin_presentation', [
                'admin_id' => $adminId,
                'presentation_id' => $presentationId
            ])->execute();
        }
    }

    public function getGroupsRight(int $groupId): array
    {
        return $this->db->select('ar.right_code_name')
            ->from('admin_right')->as('ar')
            ->join('admin_group_right')->as('agr')->on('ar.admin_right_id = agr.admin_right_id')
            ->where('agr.admin_group_id = %i', $groupId)
            ->fetchPairs('right_code_name', 'right_code_name');
    }

    public function getPageRights(int $groupId): array
    {
        return $this->db->select('page_group_id')
            ->from('page_group_admin_group')
            ->where('admin_group_id = %i', $groupId)
            ->fetchPairs('page_group_id', 'page_group_id');
    }
}
