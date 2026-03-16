<?php

namespace App\Model\AdminGroup;

use App\Model\Base\BaseEntity;

class AdminGroupEntity extends BaseEntity
{
    public mixed $admin_group_id = null;
    public ?string $admin_group_name = null;
    public mixed $pid = 0;
    public ?string $code_name = null;

    public function getId(): mixed
    {
        return $this->admin_group_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('admin_group_id', $id, self::VALUE_TYPE_INTEGER);
    }
}
