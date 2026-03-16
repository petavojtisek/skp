<?php

namespace App\Model\ModuleGroupRight;

use App\Model\Base\BaseMapper;

class ModuleGroupRightMapper extends BaseMapper
{
    protected string $tableName = 'module_group_right';
    protected string $primaryKey = 'admin_group_id';

    public function getPermissionsForGroupAndModule(int $groupId, int $moduleId): array
    {
        return $this->db->select('permission_id')
            ->from($this->tableName)
            ->where('admin_group_id = %i', $groupId)
            ->and('module_id = %i', $moduleId)
            ->fetchPairs('permission_id', 'permission_id');
    }


}
