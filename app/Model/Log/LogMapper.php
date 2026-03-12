<?php

namespace App\Model\Log;

use App\Model\Base\BaseMapper;

class LogMapper extends BaseMapper
{
    protected string $tableName = 'cms_log';
    protected string $primaryKey = 'id';


    public static function getTablenNameStatic()
    {
        return 'cms_log';
    }

    public function getLogsWithAdmin(): array
    {
        return $this->db->select('l.*, a.user_name as admin_name')
            ->from($this->tableName)->as('l')
            ->leftJoin('admin')->as('a')->on('l.admin_id = a.admin_id')
            ->orderBy('l.inserted DESC')
            ->fetchAll();
    }
}
