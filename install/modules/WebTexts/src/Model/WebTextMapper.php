<?php

namespace App\Modules\WebTexts\Model;

use App\Model\Base\BaseMapper;

class WebTextMapper extends BaseMapper
{
    protected string $tableName = 'web_text';
    protected string $primaryKey = 'web_text_id';

    public function findWebTexts(?string $code = null, ?int $limit = null, ?int $offset = null): array
    {
        $query = $this->db->select('*')->from($this->tableName);
        
        if ($code) {
            $query->where('code LIKE %like', $code);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        if ($offset !== null) {
            $query->offset($offset);
        }

        return $query->fetchAll();
    }

    public function countWebTexts(?string $code = null): int
    {
        $query = $this->db->select('COUNT(*)')->from($this->tableName);
        if ($code) {
            $query->where('code LIKE %like', $code);
        }
        return $query->fetchSingle();
    }
}
