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
}
