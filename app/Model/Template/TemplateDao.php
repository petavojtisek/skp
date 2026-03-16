<?php

namespace App\Model\Template;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class TemplateDao extends BaseDao
{
    protected string $entityName = 'Template\\TemplateEntity';

    /** @var TemplateMapper */
    protected IMapper $mapper;

    public function __construct(TemplateMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return TemplateMapper
     */
    public function getMapper(): \App\Model\Base\IMapper
    {
        return $this->mapper;
    }
}
