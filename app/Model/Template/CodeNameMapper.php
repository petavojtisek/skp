<?php

namespace App\Model\Template;

use App\Model\Base\BaseMapper;

class CodeNameMapper extends BaseMapper
{
    protected string $tableName = 'code_name';
    protected string $primaryKey = 'id';

    public function getByTemplateId(int $templateId): array
    {
        return $this->db->select('cn.*, m.module_name as module_name')
            ->from($this->tableName, 'cn')
            ->leftJoin('module', 'm')->on('m.module_id = cn.module')
            ->where('cn.template_id = %i', $templateId)
            ->fetchAll();
    }

    public function getAllowedModules(int $templateId): array
    {
        return $this->db->select('DISTINCT m.module_id, m.module_name')
            ->from($this->tableName, 'cn')
            ->join('module', 'm')->on('m.module_id = cn.module')
            ->where('cn.template_id = %i', $templateId)
            ->where('m.module_active = %s', 'Y')
            ->fetchPairs('module_id', 'module_name');
    }

    public function getAllowedCodeNames(int $templateId, int $moduleId): array
    {
        return $this->db->select('cn.code_name')
            ->from($this->tableName, 'cn')
            ->where('cn.template_id = %i AND cn.module = %i', $templateId, $moduleId)
            ->fetchPairs('code_name', 'code_name');
    }
    }

