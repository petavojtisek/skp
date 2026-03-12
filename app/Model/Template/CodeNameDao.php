<?php

namespace App\Model\Template;

use App\Model\Base\BaseDao;

class CodeNameDao extends BaseDao
{
    protected $entityName = 'Template\CodeNameEntity';

    public function __construct(CodeNameMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function findByTemplate(int $templateId): array
    {
        return $this->findAllBy(['template_id' => $templateId]) ?: [];
    }
}
