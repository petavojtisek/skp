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

}
