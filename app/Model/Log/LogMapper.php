<?php

namespace App\Model\Log;

use App\Model\Base\BaseMapper;

class LogMapper extends BaseMapper
{
    protected string $tableName = 'cms_log';
    protected string $primaryKey = 'id';

    public function getLogsWithAdmin(?int $limit = 10, ?int $offset = 0, ?string $search = null, ?string $module = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = $this->db->select('l.*, a.user_name as admin_name')
            ->from($this->tableName)->as('l')
            ->leftJoin('admin')->as('a')->on('l.admin_id = a.admin_id');

        $this->applyFilters($query, $search, $module, $dateFrom, $dateTo);

        $query->orderBy('l.created_dt DESC');

        if ($limit !== null) {
            $query->limit($limit);
        }
        if ($offset !== null) {
            $query->offset($offset);
        }

        return $query->fetchAll();
    }

    public function countLogs(?string $search = null, ?string $module = null, ?string $dateFrom = null, ?string $dateTo = null): int
    {
        $query = $this->db->select('COUNT(*)')
            ->from($this->tableName)->as('l')
            ->leftJoin('admin')->as('a')->on('l.admin_id = a.admin_id');

        $this->applyFilters($query, $search, $module, $dateFrom, $dateTo);

        return (int) $query->fetchSingle();
    }

    private function applyFilters($query, ?string $search = null, ?string $module = null, ?string $dateFrom = null, ?string $dateTo = null): void
    {
        if ($search) {
            $query->where('(l.module LIKE %like~ OR l.action LIKE %like~ OR l.name LIKE %like~ OR a.user_name LIKE %like~)', $search, $search, $search, $search);
        }

        if ($module) {
            $query->where('l.module = %s', $module);
        }

        if ($dateFrom) {
            $query->where('l.created_dt >= %d', $dateFrom);
        }

        if ($dateTo) {
            $query->where('l.created_dt <= %d', $dateTo . ' 23:59:59');
        }
    }

    public function getUniqueModules(): array
    {
        return $this->db->select('module')
            ->from($this->tableName)
            ->groupBy('module')
            ->orderBy('module ASC')
            ->fetchPairs('module', 'module');
    }
}
