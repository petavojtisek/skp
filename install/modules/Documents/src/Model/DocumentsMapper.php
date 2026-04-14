<?php

namespace App\Modules\Documents\Model;

use App\Model\Base\BaseMapper;
use App\Model\Base\IEntity;

class DocumentsMapper extends BaseMapper
{
    protected string $tableName = 'documents';
    protected string $primaryKey = 'element_id';

    public function getByComponentId(int $componentId): array
    {
        return $this->db->select('d.*, e.name, e.status_id, e.inserted as created_dt, fm.original_name')
            ->from($this->tableName, 'd')
            ->join('element', 'e')->on('e.element_id = d.element_id')
            ->leftJoin('file_manager', 'fm')->on('fm.file_id = d.file_id')
            ->where('e.component_id = %i', $componentId)
            ->fetchAll();
    }

    public function getFrontByComponentId(int $componentId): array
    {
        return $this->db->select('d.*, e.name, e.status_id, e.inserted as created_dt, fm.original_name, fm.file_id')
            ->from($this->tableName, 'd')
            ->join('element', 'e')->on('e.element_id = d.element_id')
            ->leftJoin('file_manager', 'fm')->on('fm.file_id = d.file_id')
            ->where('e.component_id = %i', $componentId)
            ->and('e.status_id = %i', C_ELEMENT_STATUS_READY)
            ->and('(e.valid_from IS NULL OR e.valid_from <= %d)', new \DateTime())
            ->and('(e.valid_to IS NULL OR e.valid_to >= %d)', new \DateTime())
            ->orderBy('e.inserted DESC')
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
