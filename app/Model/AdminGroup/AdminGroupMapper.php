<?php

namespace App\Model\AdminGroup;

use App\Model\Base\BaseMapper;

class AdminGroupMapper extends BaseMapper
{
    protected string $tableName = 'admin_group';
    protected string $primaryKey = 'admin_group_id';

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
}
