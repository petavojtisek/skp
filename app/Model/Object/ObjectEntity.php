<?php

namespace App\Model\Object;

use App\Model\Base\BaseEntity;

class ObjectEntity extends BaseEntity
{
    public mixed $object_id = null;
    public ?string $object_name = null;
    public ?string $object_code = null;
    public ?string $object_type = null;
    public mixed $object_status = 1;

    public function getId(): mixed
    {
        return $this->object_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('object_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getObjectName(): ?string { return $this->object_name; }
    public function setObjectName(?string $name): void { $this->setVariable('object_name', $name, self::VALUE_TYPE_STRING); }

    public function getObjectCode(): ?string { return $this->object_code; }
    public function setObjectCode(?string $code): void { $this->setVariable('object_code', $code, self::VALUE_TYPE_STRING); }

    public function getObjectType(): ?string { return $this->object_type; }
    public function setObjectType(?string $type): void { $this->setVariable('object_type', $type, self::VALUE_TYPE_STRING); }

    public function getObjectStatus(): mixed { return $this->object_status; }
    public function setObjectStatus(mixed $status): void { $this->setVariable('object_status', $status, self::VALUE_TYPE_INTEGER); }
}
