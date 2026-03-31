<?php

namespace App\Model\Template;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class CodeNameDao extends BaseDao
{
    protected string $entityName = 'CodeNameEntity';

    /** @var CodeNameMapper */
    protected IMapper $mapper;

    public function __construct(CodeNameMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getByTemplateId(int $templateId): array
    {
        $data = $this->mapper->getByTemplateId($templateId);
        return $this->getEntities($this->entityName, $data);
    }

    public function getAllowedModules(int $templateId): array
    {
        return $this->mapper->getAllowedModules($templateId);
    }

    public function getAllowedCodeNames(int $templateId, int $moduleId): array
    {
        return $this->mapper->getAllowedCodeNames($templateId, $moduleId);
    }
}
