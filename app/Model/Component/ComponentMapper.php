<?php

namespace App\Model\Component;

use App\Model\Base\BaseMapper;

class ComponentMapper extends BaseMapper
{
    protected string $tableName = 'component';
    protected string $primaryKey = 'id';
}
