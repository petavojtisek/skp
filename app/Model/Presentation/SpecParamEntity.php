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

    public function getPresentationId(): mixed { return $this->presentation_id; }
    public function setPresentationId(mixed $id): void { $this->setVariable('presentation_id', $id, self::VALUE_TYPE_INTEGER); }

    public function getName(): ?string { return $this->name; }
    public function setName(?string $name): void { $this->setVariable('name', $name, self::VALUE_TYPE_STRING); }

    public function getValue(): ?string { return $this->value; }
    public function setValue(?string $value): void { $this->setVariable('value', $value, self::VALUE_TYPE_STRING); }
}
