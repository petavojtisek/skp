<?php

namespace App\Model\ModulePermission;

use App\Model\Base\BaseEntity;

class ModulePermissionEntity extends BaseEntity
{
    public mixed $module_permission_id = null;
    public ?int $module_id = null;
    public ?string $permission_name = null;

    public function getId(): mixed
    {
        return $this->module_permission_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('module_permission_id', $id, self::VALUE_TYPE_INTEGER);
    }
}
