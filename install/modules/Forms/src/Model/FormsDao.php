<?php

namespace App\Modules\Forms\Model;

use App\Model\Base\BaseDao;

class FormsDao extends BaseDao
{
    protected string $entityName = 'App\Modules\Forms\Model\FormsEntity';

    public function getByComponentId(int $componentId): array
    {
        $data = $this->mapper->getByComponentId($componentId);
        return $this->getEntities($this->entityName, $data);
    }
}
