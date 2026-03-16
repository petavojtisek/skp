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
}
