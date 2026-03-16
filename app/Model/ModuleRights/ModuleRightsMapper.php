<?php

namespace App\Model\ModuleRights;

use App\Model\Base\BaseMapper;

class ModuleRightsMapper extends BaseMapper
{
    protected string $tableName = 'module_permission';
    protected string $primaryKey = 'module_permission_id';
}
