<?php

namespace App\Modules\ContentVersion\Model;

use App\Model\Base\BaseMapper;

class ContentVersionMapper extends BaseMapper
{
    protected string $tableName = 'content_version';
    protected string $primaryKey = 'element_id';

    public function getByComponentId(int $componentId): array
    {
        return $this->db->select('cv.*')
            ->from($this->tableName, 'cv')
            ->join('version', 'v')->on('v.element_id = cv.element_id')
            ->where('v.component_id = %i', $componentId)
            ->fetchAll();
    }
}
