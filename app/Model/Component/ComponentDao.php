<?php

namespace App\Model\Component;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;
class ComponentDao extends BaseDao
{
    protected string $entityName = 'Component\\ComponentEntity';

    /** @var ComponentMapper */
    protected IMapper $mapper;

    public function __construct(ComponentMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getByPageId(int $pageId): array
    {
        $data = $this->mapper->getByPageId($pageId);
        return $this->getEntities($this->entityName, $data);
    }

    public function getExistingNotOnPage(int $pageId, int $templateId): array
    {
        $data = $this->mapper->getExistingNotOnPage($pageId, $templateId);
        return $this->getEntities($this->entityName, $data);
    }
}

