<?php

namespace App\Model\Module;

use App\Model\Base\BaseMapper;

class ModuleMapper extends BaseMapper
{
    protected string $tableName = 'module';
    protected string $primaryKey = 'module_id';

    public function getModuleByInstallId(int $installId): ?array
    {
        return $this->db->select('*')
            ->from($this->tableName)
            ->where('install_id = %i', $installId)
            ->fetch();
    }

    public function getGroupModuleRights(int $groupId): array
    {
        return $this->db->select('m.module_code_name, mp.right_code_name')
            ->from('module_group_right')->as('mgr')
            ->join('module')->as('m')->on('mgr.module_id = m.module_id')
            ->join('module_permission')->as('mp')->on('mgr.permission_id = mp.module_permission_id')
            ->where('mgr.admin_group_id = %i', $groupId)
            ->fetchAll();
    }
}
