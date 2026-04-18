<?php

namespace App\Modules\SystemConstants\Model;

use App\Model\Base\BaseEntity;

class SystemConstantsEntity extends BaseEntity
{
    public mixed $system_constant_id = null;
    public ?string $code = null;
    public ?string $value = null;

    public function getId(): mixed
    {
        return $this->system_constant_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('system_constant_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->setVariable('code', $code, self::VALUE_TYPE_STRING);
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->setVariable('value', $value, self::VALUE_TYPE_STRING);
    }
}
