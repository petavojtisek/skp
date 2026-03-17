<?php

namespace App\Model\Admin;

use App\Model\Base\BaseMapper;

class AdminMapper extends BaseMapper
{
    protected string $tableName = 'admin';
    protected string $primaryKey = 'admin_id';

    public function getActiveAdmins(?array $groupIds = null): array
    {
        $query = $this->db->select('a.*, g.admin_group_name')
            ->from($this->tableName)->as('a')
            ->leftJoin('admin_group')->as('g')->on('a.admin_group_id = g.admin_group_id')
            ->where('a.status != %i', constant('C_ADMINISTRATOR_STATUS_REMOVED'));

        if ($groupIds !== null) {
            $query->where('a.admin_group_id IN %in', $groupIds);
        }

        return $query->fetchAll();
    }
}
