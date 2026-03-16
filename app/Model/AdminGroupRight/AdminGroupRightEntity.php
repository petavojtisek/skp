<?php

namespace App\Model\AdminGroupRight;

use App\Model\Base\BaseEntity;

class AdminGroupRightEntity extends BaseEntity
{
    public mixed $admin_group_id = null;
    public mixed $admin_right_id = null;

    public function getId(): mixed
    {
        return null; // Composite key or no primary key
    }

    public function setId(mixed $id): void
    {
    }
}
