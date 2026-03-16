<?php

namespace App\Model\ModuleRight;

use App\Model\Base\BaseEntity;

class ModuleRightEntity extends BaseEntity
{
    public mixed $module_id = null;
    public ?int $permission_id = null;

    public function getId(): mixed
    {
        return $this->module_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('module_id', $id, self::VALUE_TYPE_INTEGER);
    }
}
