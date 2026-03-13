<?php

namespace App\Model\Log;

use App\Model\Base\BaseMapper;

class LogMapper extends BaseMapper
{
    protected string $tableName = 'cms_log';
    protected string $primaryKey = 'id';



    public function getLogsWithAdmin(?int $limit = 10, ?int  $offset = 0): array
    {
        return $this->db->select('l.*, a.user_name as admin_name')
            ->from($this->tableName)->as('l')
            ->leftJoin('admin')->as('a')->on('l.admin_id = a.admin_id')
            ->orderBy('l.created_dt DESC')
            ->limit($limit)
            ->offset($offset)
            ->fetchAll();
    }
}
