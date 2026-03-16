<?php

namespace App\Model\Object;

use App\Model\Base\BaseMapper;

class ObjectMapper extends BaseMapper
{
    protected string $tableName = 'object';
    protected string $primaryKey = 'object_id';
}
