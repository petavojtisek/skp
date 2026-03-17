<?php

namespace App\Model\AdminGroupRight;

use App\Model\Base\BaseEntity;

class AdminGroupRightEntity extends BaseEntity
{
    public mixed $admin_group_right_id = null;
    public mixed $admin_group_id = null;
    public mixed $admin_right_id = null;

    public function getId(): mixed
    {
        return $this->admin_group_right_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('admin_group_right_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getAdminGroupRightId(): mixed
    {
        return $this->admin_group_right_id;
    }

    public function setAdminGroupRightId(mixed $admin_group_right_id): void
    {
        $this->setVariable('admin_group_right_id', $admin_group_right_id, self::VALUE_TYPE_INTEGER);
    }

    public function getAdminGroupId(): ?int
    {
        return $this->admin_group_id;
    }

    public function setAdminGroupId(?int $admin_group_id): void
    {
        $this->setVariable('admin_group_id', $admin_group_id, self::VALUE_TYPE_INTEGER);
    }

    public function getAdminRightId(): ?int
    {
        return $this->admin_right_id;
    }

    public function setAdminRightId(?int $admin_right_id): void
    {
        $this->setVariable('admin_right_id', $admin_right_id, self::VALUE_TYPE_INTEGER);
    }
}
