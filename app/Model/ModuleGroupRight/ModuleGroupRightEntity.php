<?php

namespace App\Model\ModuleGroupRight;

use App\Model\Base\BaseEntity;

class ModuleGroupRightEntity extends BaseEntity
{
    public mixed $admin_group_id = null;
    public ?int $module_id = null;
    public ?int $permission_id = null;

    public function getId(): mixed
    {
        return $this->admin_group_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('admin_group_id', $id, self::VALUE_TYPE_INTEGER);
    }
}
