<?php

namespace App\Model\ModuleRight;

use App\Model\Base\BaseEntity;

class ModuleRightEntity extends BaseEntity
{
    public mixed $module_right_id = null;
    public mixed $module_id = null;
    public ?int $permission_id = null;

    public function getId(): mixed
    {
        return $this->module_right_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('module_right_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getModuleRightId(): mixed
    {
        return $this->module_right_id;
    }

    public function setModuleRightId(mixed $module_right_id): void
    {
        $this->setVariable('module_right_id', $module_right_id, self::VALUE_TYPE_INTEGER);
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
