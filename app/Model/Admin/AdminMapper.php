<?php

namespace App\Model\Admin;

use App\Model\Base\BaseMapper;

class AdminMapper extends BaseMapper
{
    protected $tableName = 'admin';
    protected $primaryKey = 'admin_id';

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
}
