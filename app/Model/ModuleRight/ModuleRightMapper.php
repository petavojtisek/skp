<?php

namespace App\Model\ModuleRight;

use App\Model\Base\BaseMapper;

class ModuleRightMapper extends BaseMapper
{
    protected string $tableName = 'module_right';
    protected string $primaryKey = 'module_right_id';

    public function getPermissionsByModule(int $moduleId): array
    {
        return $this->db->select('permission_id')
            ->from($this->tableName)
            ->where('module_id = %i', $moduleId)
            ->fetchPairs('permission_id', 'permission_id');
    }
}
