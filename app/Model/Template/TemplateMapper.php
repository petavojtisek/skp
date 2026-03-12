<?php

namespace App\Model\Template;

use App\Model\Base\BaseMapper;

class TemplateMapper extends BaseMapper
{
    protected string $tableName = 'template';
    protected string $primaryKey = 'template_id';
}
