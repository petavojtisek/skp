<?php

namespace App\Model\FileManager;

use App\Model\Base\BaseMapper;

class FileManagerMapper extends BaseMapper
{
    protected string $tableName = 'file_manager';
    protected string $primaryKey = 'file_id';

    public function getFilesByElement(string $sourceType, int $elementId): array
    {
        return $this->db->select('*')
            ->from($this->tableName)
            ->where('source_type = %s', $sourceType)
            ->and('element_id = %i', $elementId)
            ->fetchAll() ?: [];
    }
}
