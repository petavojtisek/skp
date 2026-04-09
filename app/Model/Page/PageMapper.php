<?php

namespace App\Model\Page;

use App\Model\Base\BaseMapper;

class PageMapper extends BaseMapper
{
    protected string $tableName = 'page';
    protected string $primaryKey = 'page_id';

    public function getByRewrite(string $rewrite, int $presentationId): ?array
    {
        return $this->db->select('*')
            ->from($this->tableName)
            ->where('page_rewrite = %s', $rewrite)
            ->and('presentation_id = %i', $presentationId)
            ->fetch();
    }
}
