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

    public function getGroupName(): ?string
    {
        return $this->admin_group_name;
    }

    public function setGroupName(?string $name): void
    {
        $this->setVariable('admin_group_name', $name, self::VALUE_TYPE_STRING);
    }

    public function getPid(): mixed { return $this->pid; }
    public function setPid(mixed $pid): void { $this->setVariable('pid', $pid, self::VALUE_TYPE_INTEGER); }

    public function getCodeName(): ?string { return $this->code_name; }
    public function setCodeName(?string $code_name): void { $this->setVariable('code_name', $code_name, self::VALUE_TYPE_STRING); }
}
