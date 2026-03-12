<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseMapper;

class PresentationMapper extends BaseMapper
{
    protected string $tableName = 'presentation';
    protected string $primaryKey = 'presentation_id';
}
