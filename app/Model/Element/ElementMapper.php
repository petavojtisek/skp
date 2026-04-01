<?php

namespace App\Model\Element;

use App\Model\Base\BaseMapper;

class ElementMapper extends BaseMapper
{
    protected string $tableName = 'element';
    protected string $primaryKey = 'element_id';
}
