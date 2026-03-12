<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseMapper;

class ComponentActionMapper extends BaseMapper
{
    protected string $tableName = 'presentation_component_action';
    protected string $primaryKey = 'element_id';
}
