<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseEntity;

class ComponentActionEntity extends BaseEntity
{
    public mixed $element_id = null;
    public mixed $presentation_id = null;
    public mixed $component_id = null;
    public ?string $module = null;
    public ?string $action = null;
    public mixed $params = null;

    public function getId(): mixed { return $this->element_id; }
    public function setId(mixed $id): void { $this->setVariable('element_id', $id, self::VALUE_TYPE_INTEGER); }
}
