<?php

namespace App\Model\Install;

use App\Model\Base\BaseMapper;

class InstallMapper extends BaseMapper
{
    protected string $tableName = 'install';
    protected string $primaryKey = 'install_id';

    public function getModuleByInstallId(int $installId): ?array
    {
        return $this->db->select('*')
            ->from('module')
            ->where('install_id = %i', $installId)
            ->fetch();
    }
}
