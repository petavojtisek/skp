<?php

namespace App\Model\Component;

use App\Model\Base\BaseEntity;

class ComponentEntity extends BaseEntity
{
    public mixed $id = null;
    public ?string $name = null;

    public function getId(): mixed
    {
        return $this->id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->setVariable('name', $name, self::VALUE_TYPE_STRING);
    }
}
