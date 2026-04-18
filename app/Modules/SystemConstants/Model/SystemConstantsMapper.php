<?php

namespace App\Modules\SystemConstants\Model;

use App\Model\Base\BaseMapper;

class SystemConstantsMapper extends BaseMapper
{
    protected string $tableName = 'system_constants';
    protected string $primaryKey = 'system_constant_id';

    public function findSystemConstants(?string $search = null, int $limit = 10, int $offset = 0): array
    {
        $selection = $this->db->select('*')->from($this->tableName);
        if ($search) {
            $selection->where('code LIKE %like~ OR value LIKE %like~', $search, $search);
        }
        return $selection->limit($limit)->offset($offset)->fetchAll();
    }

    public function countSystemConstants(?string $search = null): int
    {
        $selection = $this->db->select('COUNT(*)')->from($this->tableName);
        if ($search) {
            $selection->where('code LIKE %like~ OR value LIKE %like~', $search, $search);
        }
        return $selection->fetchSingle();
    }
}
