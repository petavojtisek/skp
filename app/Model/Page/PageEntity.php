<?php

namespace App\Model\Page;

use App\Model\Base\BaseEntity;

class PageEntity extends BaseEntity
{
    public mixed $id = null;
    public ?string $name = null;
    public ?string $url = null;
    public ?string $title = null;
    public mixed $active = null;

    public function getId(): mixed { return $this->id; }
    public function setId(mixed $id): void { $this->setVariable('id', $id, self::VALUE_TYPE_INTEGER); }
}
