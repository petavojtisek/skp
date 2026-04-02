<?php

namespace App\Modules\ContentVersion\Model;

use App\Model\Base\BaseMapper;

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
}
