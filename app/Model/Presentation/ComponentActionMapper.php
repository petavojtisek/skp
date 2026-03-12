<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseMapper;

class ComponentActionMapper extends BaseMapper
{
    protected $tableName = 'presentation_component_action';
    protected $primaryKey = 'element_id';
}
