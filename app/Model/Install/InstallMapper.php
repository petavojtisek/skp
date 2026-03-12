<?php

namespace App\Model\Install;

use App\Model\Base\BaseMapper;

class InstallMapper extends BaseMapper
{
    protected string $tableName = 'install';
    protected string $primaryKey = 'install_id';
}
