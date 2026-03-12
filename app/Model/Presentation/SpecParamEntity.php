<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseEntity;

class SpecParamEntity extends BaseEntity
{
    public mixed $spec_param_id = null;
    public mixed $presentation_id = null;
    public ?string $name = null;
    public ?string $value = null;

    public function getId(): mixed
    {
        return $this->spec_param_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('spec_param_id', $id, self::VALUE_TYPE_INTEGER);
    }
}
