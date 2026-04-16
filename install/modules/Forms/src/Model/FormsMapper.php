<?php

namespace App\Modules\Forms\Model;

use App\Model\Base\BaseMapper;
use App\Model\Base\IEntity;

class FormsMapper extends BaseMapper
{
    protected string $tableName = 'forms';
    protected string $primaryKey = 'element_id';

    public function getByComponentId(int $componentId): array
    {
        return $this->db->select('f.*, e.name, e.status_id, e.inserted as created_dt')
            ->from($this->tableName, 'f')
            ->join('element', 'e')->on('e.element_id = f.element_id')
            ->where('e.component_id = %i', $componentId)
            ->fetchAll();
    }

    public function save(IEntity $entity): IEntity
    {
        if ($this->rowExist([$this->primaryKey => $entity->getId()])) {
            $this->update($entity);
        } else {
            $this->db->insert($this->tableName, $entity->getEntityData())->execute();
        }

        $this->logChanges($entity, 'save');
        return $entity;
    }
}
