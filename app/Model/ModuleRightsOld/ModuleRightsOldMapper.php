<?php

namespace App\Model\ModuleRightsOld;

use App\Model\Base\BaseMapper;

class ModuleRightsOldMapper extends BaseMapper
{
    protected string $tableName = 'module_permission';
    protected string $primaryKey = 'module_permission_id';

    public function getModulePermissions(int $moduleId, int $adminGroupId): array
    {
        return $this->db->select('mp.module_permission_id, mp.name, (mgr.admin_group_id IS NOT NULL) as is_active')
            ->from('module_rights')->as('mr')
            ->join('module_permission')->as('mp')->on('mr.permission_id = mp.module_permission_id')
            ->leftJoin('module_group_right')->as('mgr')->on('mr.module_id = mgr.module_id AND mr.permission_id = mgr.permission_id AND mgr.admin_group_id = %i', $adminGroupId)
            ->where('mr.module_id = %i', $moduleId)
            ->fetchAll();
    }

    public function toggleModuleGroupRight(int $adminGroupId, int $moduleId, int $permissionId, bool $state): void
    {
        if ($state) {
            $exists = $this->db->fetch('SELECT 1 FROM module_group_right WHERE admin_group_id = %i AND module_id = %i AND permission_id = %i', $adminGroupId, $moduleId, $permissionId);
            if (!$exists) {
                $this->db->query('INSERT INTO module_group_right', [
                    'admin_group_id' => $adminGroupId,
                    'module_id' => $moduleId,
                    'permission_id' => $permissionId
                ]);
            }
        } else {
            $this->db->query('DELETE FROM module_group_right WHERE admin_group_id = %i AND module_id = %i AND permission_id = %i', $adminGroupId, $moduleId, $permissionId);
        }
    }
}
