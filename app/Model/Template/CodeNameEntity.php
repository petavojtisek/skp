<?php

namespace App\Model\Entity;

use App\Model\Base\BaseEntity;

class CodeNameEntity extends BaseEntity
{
    public mixed $id = null;
    public mixed $template_id = null;
    public ?string $module = null;
    public ?string $code_name = null;

    public function getId(): mixed { return $this->id; }
    public function setId(mixed $id): void { $this->setVariable('id', $id, self::VALUE_TYPE_INTEGER); }
}
