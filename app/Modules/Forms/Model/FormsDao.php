<?php

namespace App\Modules\Forms\Model;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class FormsDao extends BaseDao
{
    protected string $entityName = 'App\Modules\Forms\Model\FormsEntity';

    protected IMapper $mapper;


    public function __construct(FormsMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getByComponentId(int $componentId): array
    {
        $data = $this->mapper->getByComponentId($componentId);
        return $this->getEntities($this->entityName, $data);
    }
}
