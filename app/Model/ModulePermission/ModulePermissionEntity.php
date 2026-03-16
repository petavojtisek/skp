<?php

namespace App\Model\ModulePermission;

use App\Model\Base\BaseEntity;

class ModulePermissionEntity extends BaseEntity
{
    public mixed $permission_id = null;
    public ?string $name = null;
    public ?string $right_code_name = null;

    public function getId(): mixed
    {
        return $this->permission_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('permission_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->setVariable('name', $name, self::VALUE_TYPE_STRING);
    }

    public function getRightCodeName(): ?string
    {
        return $this->right_code_name;
    }

    public function setRightCodeName(?string $right_code_name): void
    {
        $this->setVariable('right_code_name', $right_code_name, self::VALUE_TYPE_STRING);
    }
}
