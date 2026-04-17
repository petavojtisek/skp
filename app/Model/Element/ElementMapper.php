<?php

namespace App\Model\Element;

use App\Model\Base\BaseMapper;

class ElementMapper extends BaseMapper
{
    protected string $tableName = 'element';
    protected string $primaryKey = 'element_id';



    public function getActiveElementId(int $componentId): ?int
    {
        return $this->db->select('element_id')
            ->from($this->tableName)
            ->where('component_id = %i', $componentId)
            ->fetchSingle();
    }
}
