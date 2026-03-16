<?php

namespace App\Model\ModulePermission;

use App\Model\Base\BaseMapper;

class ModulePermissionMapper extends BaseMapper
{
    protected string $tableName = 'module_permission';
    protected string $primaryKey = 'module_permission_id';

    public function getPermissionsByIds(array $ids): array
    {
        return $this->db->select('*')
            ->from($this->tableName)
            ->where('permission_id IN %in', $ids)
            ->fetchAssoc('permission_id');
    }
}
