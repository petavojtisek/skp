<?php

namespace App\Model\Component;

use App\Model\Base\BaseMapper;

class ComponentMapper extends BaseMapper
{
    protected string $tableName = 'component';
    protected string $primaryKey = 'component_id';

    public function getByPageId(int $pageId): array
    {
        return $this->db->select('c.*, m.module_class_name, m.module_code_name')
            ->from($this->tableName, 'c')
            ->join('page_component', 'pc')->on('pc.component_id = c.component_id')
            ->join('module', 'm')->on('m.module_id = c.module_id')
            ->where('pc.page_id = %i', $pageId)
            ->fetchAll();
    }

    public function getExistingNotOnPage(int $pageId, int $templateId): array
    {
        // Components that are NOT on this page AND are allowed by the template
        return $this->db->select('c.*, m.module_name, m.module_code_name')
            ->from($this->tableName, 'c')
            ->join('module', 'm')->on('m.module_id = c.module_id')
            ->join('code_name', 'cn')->on('cn.module = c.module_id AND cn.code_name = c.code_name')
            ->where('cn.template_id = %i', $templateId)
            ->where('c.component_id NOT IN (SELECT component_id FROM page_component WHERE page_id = %i)', $pageId)
            ->fetchAll();
    }
}
