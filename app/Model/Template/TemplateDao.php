<?php

namespace App\Model\Template;

use App\Model\Base\BaseDao;

class TemplateDao extends BaseDao
{
    protected $entityName = 'Template\TemplateEntity';

    public function __construct(TemplateMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
