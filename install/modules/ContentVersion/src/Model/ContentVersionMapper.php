<?php

namespace App\Modules\ContentVersion\Model;

use App\Model\Base\BaseMapper;
use App\Model\Base\IEntity;

class ContentVersionMapper extends BaseMapper
{
    protected string $tableName = 'content_version';
    protected string $primaryKey = 'element_id';

    public function getByComponentId(int $componentId): array
    {
        return $this->db->select('cv.*, e.name, e.status_id, e.inserted as created_dt')
            ->from($this->tableName, 'cv')
            ->join('element', 'e')->on('e.element_id = cv.element_id')
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
