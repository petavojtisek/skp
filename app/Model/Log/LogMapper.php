<?php

namespace App\Model\Log;

use App\Model\Base\BaseMapper;

class LogMapper extends BaseMapper
{
    protected $tableName = 'cms_log';
    protected $primaryKey = 'id';

    public function getLogsWithAdmin(): array
    {
        return $this->db->select('l.*, a.user_name as admin_name')
            ->from($this->tableName)->as('l')
            ->leftJoin('admin')->as('a')->on('l.admin_id = a.admin_id')
            ->orderBy('l.inserted DESC')
            ->fetchAll();
    }
}
