<?php

namespace App\Model\Config;

use App\Model\Base\BaseEntity;

class ConfigEntity extends BaseEntity
{
    public mixed $config_id = null;
    public ?string $item = null;
    public ?string $value = null;

    public function getId(): mixed { return $this->config_id; }
    public function setId(mixed $id): void { $this->setVariable('config_id', $id, self::VALUE_TYPE_INTEGER); }

    public function getItem(): ?string
    {
        return $this->item;
    }

    public function setItem(?string $item): void
    {
        $this->setVariable('item', $item, self::VALUE_TYPE_STRING);
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
