<?php

namespace App\Modules\ContentVersion\Model;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class ContentVersionDao extends BaseDao
{
    protected string $entityName = 'App\Modules\ContentVersion\Model\ContentVersionEntity';

    /** @var ContentVersionMapper */
    protected IMapper $mapper;

    public function __construct(ContentVersionMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getByComponentId(int $componentId): array
    {
        $data = $this->mapper->getByComponentId($componentId);
        return $this->getEntities($this->entityName, $data);
    }
}
