<?php

namespace App\Modules\Forms\Model;

use App\Model\Base\BaseMapper;

class FormsMapper extends BaseMapper
{
    protected string $tableName = 'form_data';
    protected string $primaryKey = 'id';

    public function findForms(int $limit, int $offset, ?string $search = null): array
    {
        $selection = $this->db->select('*')->from($this->tableName);

        if ($search) {
            $selection->where('form_name LIKE %like~ OR ip_address LIKE %like~ OR data LIKE %like~', $search, $search, $search);
        }

        if ($limit) {
            $selection->limit($limit);
        }

        if ($offset) {
            $selection->offset($offset);
        }

        return $selection->orderBy('created_dt DESC')->fetchAll();
    }

    public function countForms(?string $search = null): int
    {
        $selection = $this->db->select('COUNT(*)')->from($this->tableName);

        if ($search) {
            $selection->where('form_name LIKE %like~ OR ip_address LIKE %like~ OR data LIKE %like~', $search, $search, $search);
        }

        return (int)$selection->fetchSingle();
    }
}
