<?php

namespace App\Modules\Members\Model;

use App\Model\Base\BaseMapper;

class MembersMapper extends BaseMapper
{
    protected string $tableName = 'members';
    protected string $primaryKey = 'member_id';

    public function getNextMemberNumber(): int
    {
        $year = (int)date('Y');
        $min = $year * 1000000;
        $max = ($year + 1) * 1000000 - 1;

        $lastNumber = $this->db->select('MAX(member_number)')
            ->from($this->tableName)
            ->where('member_number >= %i', $min)
            ->where('member_number <= %i', $max)
            ->fetchSingle();

        if (!$lastNumber) {
            return $min + 1;
        }

        return (int)$lastNumber + 1;
    }

    public function findMembers(int $limit, int $offset, ?string $search = null, ?string $source = null): array
    {
        $selection = $this->db->select('*')->from($this->tableName);

        if ($search) {
            $selection->where('name LIKE %like~ OR surname LIKE %like~ OR member_number LIKE %like~ OR email LIKE %like~ OR city LIKE %like~ OR zip LIKE %like~', $search, $search, $search, $search, $search, $search);
        }

        if ($source) {
            $selection->where('source = %s', $source);
        }

        if ($limit) {
            $selection->limit($limit);
        }

        if ($offset) {
            $selection->offset($offset);
        }

        return $selection->orderBy('surname ASC, name ASC')->fetchAll();
    }

    public function countMembers(?string $search = null, ?string $source = null): int
    {
        $selection = $this->db->select('COUNT(*)')->from($this->tableName);

        if ($search) {
            $selection->where('name LIKE %like~ OR surname LIKE %like~ OR member_number LIKE %like~ OR email LIKE %like~ OR city LIKE %like~ OR zip LIKE %like~', $search, $search, $search, $search, $search, $search);
        }

        if ($source) {
            $selection->where('source = %s', $source);
        }

        return (int)$selection->fetchSingle();
    }
}
