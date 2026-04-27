<?php

namespace App\Modules\FormsData\Model;

use App\Model\Base\BaseMapper;

class FormsDataMapper extends BaseMapper
{
    protected string $tableName = 'form_data';
    protected string $primaryKey = 'id';

    public function findFormsData(int $limit, int $offset, ?string $search = null): array
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

    public function countFormsData(?string $search = null): int
    {
        $selection = $this->db->select('COUNT(*)')->from($this->tableName);

        if ($search) {
            $selection->where('form_name LIKE %like~ OR ip_address LIKE %like~ OR data LIKE %like~', $search, $search, $search);
        }

        return (int)$selection->fetchSingle();
    }

    public function findLastByFormName(string $formName, int $limit): array
    {
        return $this->db->select('*')
            ->from($this->tableName)
            ->where('form_name = %s', $formName)
            ->orderBy('created_dt DESC')
            ->limit($limit)
            ->fetchAll();
    }
}
