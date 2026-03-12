<?php

namespace App\Model\Page;

use App\Model\Base\BaseMapper;

class PageMapper extends BaseMapper
{
    protected string $tableName = 'page';
    protected string $primaryKey = 'id';
}
