<?php

namespace App\Model\Lookup;

use App\Model\Base\BaseEntity;

class LookupEntity extends BaseEntity
{
    public mixed $lookup_id = null;
    public mixed $parent_id = null;
    public ?string $item = null;
    public ?string $constant = null;

    public function getId(): mixed { return $this->lookup_id; }
    public function setId(mixed $id): void { $this->setVariable('lookup_id', $id, self::VALUE_TYPE_INTEGER); }

    public function getParentId(): mixed { return $this->parent_id; }
    public function setParentId(mixed $parent_id): void { $this->setVariable('parent_id', $parent_id, self::VALUE_TYPE_INTEGER); }

    public function getItem(): ?string { return $this->item; }
    public function setItem(?string $item): void { $this->setVariable('item', $item, self::VALUE_TYPE_STRING); }

    public function getConstant(): ?string { return $this->constant; }
    public function setConstant(?string $constant): void { $this->setVariable('constant', $constant, self::VALUE_TYPE_STRING); }
}
