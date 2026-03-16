<?php

namespace App\Model\AdminRight;

use App\Model\Base\BaseEntity;

class AdminRightEntity extends BaseEntity
{
    public mixed $admin_right_id = null;
    public ?string $name = null;
    public ?string $right_code_name = null;

    public function getId(): mixed
    {
        return $this->admin_right_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('admin_right_id', $id, self::VALUE_TYPE_INTEGER);
    }
}
