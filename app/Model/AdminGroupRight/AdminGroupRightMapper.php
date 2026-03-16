<?php

namespace App\Model\AdminGroupRight;

use App\Model\Base\BaseMapper;

class AdminGroupRightMapper extends BaseMapper
{
    protected string $tableName = 'admin_group_right';
    protected string $primaryKey = 'admin_group_id'; // Dummy primary key for BaseMapper

    public function toggleRight(int $groupId, int $rightId, bool $state): void
    {
        if ($state) {
            $exists = $this->db->fetch('SELECT 1 FROM admin_group_right WHERE admin_group_id = ? AND admin_right_id = ?', $groupId, $rightId);
            if (!$exists) {
                $this->db->query('INSERT INTO admin_group_right', [
                    'admin_group_id' => $groupId,
                    'admin_right_id' => $rightId
                ]);
            }
        } else {
            $this->db->query('DELETE FROM admin_group_right WHERE admin_group_id = ? AND admin_right_id = ?', $groupId, $rightId);
        }
    }

    public function getGroupRightsIds(int $groupId): array
    {
        return $this->db->select('admin_right_id')
            ->from($this->tableName)
            ->where('admin_group_id = ?', $groupId)
            ->fetchPairs('admin_right_id', 'admin_right_id');
    }

    public function getGroupRightsCodes(int $groupId): array
    {
        return $this->db->select('ar.right_code_name, 1 as val')
            ->from('admin_right')->as('ar')
            ->join($this->tableName)->as('agr')->on('ar.admin_right_id = agr.admin_right_id')
            ->where('agr.admin_group_id = ?', $groupId)
            ->fetchPairs('right_code_name', 'val');
    }
}
