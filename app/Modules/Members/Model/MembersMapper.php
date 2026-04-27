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
        $min = $year * 1000000 + 100000;
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

    public function findMembers(int $limit, int $offset, ?string $search = null, ?string $source = null, ?bool $registrationEmail = null, ?bool $registrationConfirm = null, ?bool $paymentConfirm = null, ?bool $isPaid = null, ?bool $active = null): array
    {
        $selection = $this->db->select('*')->from($this->tableName);

        if ($search) {
            $selection->where('name LIKE %like~ OR surname LIKE %like~ OR member_number LIKE %like~ OR email LIKE %like~ OR city LIKE %like~ OR zip LIKE %like~', $search, $search, $search, $search, $search, $search);
        }

        if ($source) {
            $selection->where('source = %s', $source);
        }

        if ($registrationEmail !== null) {
            $selection->where($registrationEmail ? 'registration_email_dt IS NOT NULL' : 'registration_email_dt IS NULL');
        }

        if ($registrationConfirm !== null) {
            $selection->where($registrationConfirm ? 'registration_confirm_email_dt IS NOT NULL' : 'registration_confirm_email_dt IS NULL');
        }

        if ($paymentConfirm !== null) {
            $selection->where($paymentConfirm ? 'payment_confirm_email_dt IS NOT NULL' : 'payment_confirm_email_dt IS NULL');
        }

        if ($isPaid !== null) {
            $selection->where($isPaid ? 'last_member_payment IS NOT NULL' : 'last_member_payment IS NULL');
        }

        if ($active !== null) {
            $selection->where('active = %i', $active ? 1 : 0);
        }

        if ($limit) {
            $selection->limit($limit);
        }

        if ($offset) {
            $selection->offset($offset);
        }

        return $selection->orderBy('surname ASC, name ASC')->fetchAll();
    }

    public function countMembers(?string $search = null, ?string $source = null, ?bool $registrationEmail = null, ?bool $registrationConfirm = null, ?bool $paymentConfirm = null, ?bool $isPaid = null, ?bool $active = null): int
    {
        $selection = $this->db->select('COUNT(*)')->from($this->tableName);

        if ($search) {
            $selection->where('name LIKE %like~ OR surname LIKE %like~ OR member_number LIKE %like~ OR email LIKE %like~ OR city LIKE %like~ OR zip LIKE %like~', $search, $search, $search, $search, $search, $search);
        }

        if ($source) {
            $selection->where('source = %s', $source);
        }

        if ($registrationEmail !== null) {
            $selection->where($registrationEmail ? 'registration_email_dt IS NOT NULL' : 'registration_email_dt IS NULL');
        }

        if ($registrationConfirm !== null) {
            $selection->where($registrationConfirm ? 'registration_confirm_email_dt IS NOT NULL' : 'registration_confirm_email_dt IS NULL');
        }

        if ($paymentConfirm !== null) {
            $selection->where($paymentConfirm ? 'payment_confirm_email_dt IS NOT NULL' : 'payment_confirm_email_dt IS NULL');
        }

        if ($isPaid !== null) {
            $selection->where($isPaid ? 'last_member_payment IS NOT NULL' : 'last_member_payment IS NULL');
        }

        if ($active !== null) {
            $selection->where('active = %i', $active ? 1 : 0);
        }

        return (int)$selection->fetchSingle();
    }

    public function findLatestRegistrations(int $limit): array
    {
        return $this->db->select('*')
            ->from($this->tableName)
            ->orderBy('created_dt DESC, member_id DESC')
            ->limit($limit)
            ->fetchAll();
    }
}
