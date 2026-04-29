<?php

namespace App\Model\FileManager;

use App\Model\Base\BaseMapper;

class FileManagerMapper extends BaseMapper
{
    protected string $tableName = 'file_manager';
    protected string $primaryKey = 'file_id';

    public function getFilesByPath(string $baseType, string $subDir): array
    {
        $path = $baseType . ($subDir ? '/' . $subDir : '');
        return $this->db->select('*')
            ->from($this->tableName)
            ->where('path = %s', $path)
            ->orderBy('sort_order ASC, created_dt DESC')
            ->fetchAll() ?: [];
    }

    public function getFilesByElement(string $sourceType, int $elementId): array
    {
        return $this->db->select('*')
            ->from($this->tableName)
            ->where('source_type = %s', $sourceType)
            ->and('element_id = %i', $elementId)
            ->orderBy('sort_order ASC, created_dt DESC')
            ->fetchAll() ?: [];
    }
}
