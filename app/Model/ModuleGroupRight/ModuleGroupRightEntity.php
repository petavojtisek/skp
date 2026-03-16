<?php

namespace App\Model\ModuleGroupRight;

use App\Model\Base\BaseEntity;

class ModuleGroupRightEntity extends BaseEntity
{
    public mixed $module_group_right_id = null;
    public ?int $admin_group_id = null;
    public ?int $module_id = null;
    public ?int $permission_id = null;

    public function getId(): mixed
    {
        return $this->module_group_right_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('module_group_right_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getModuleGroupRightId(): mixed
    {
        return $this->module_group_right_id;
    }

    public function setModuleGroupRightId(mixed $module_group_right_id): void
    {
        $this->setVariable('module_group_right_id', $module_group_right_id, self::VALUE_TYPE_INTEGER);
    }


    public function getAdminGroupId(): ?int
    {
        return $this->admin_group_id;
    }

    public function setAdminGroupId(?int $admin_group_id): void
    {
        $this->setVariable('admin_group_id', $admin_group_id, self::VALUE_TYPE_INTEGER);
    }

    public function getModuleId(): ?int
    {
        return $this->module_id;
    }

    public function setModuleId(?int $module_id): void
    {
        $this->setVariable('module_id', $module_id, self::VALUE_TYPE_INTEGER);
    }

    public function getPermissionId(): ?int
    {
        return $this->permission_id;
    }

    public function setPermissionId(?int $permission_id): void
    {
        $this->setVariable('permission_id', $permission_id, self::VALUE_TYPE_INTEGER);
    }
}
