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

    public function getPresentationId(): mixed { return $this->presentation_id; }
    public function setPresentationId(mixed $presentation_id): void { $this->setVariable('presentation_id', $presentation_id, self::VALUE_TYPE_INTEGER); }

    public function getComponentId(): mixed { return $this->component_id; }
    public function setComponentId(mixed $component_id): void { $this->setVariable('component_id', $component_id, self::VALUE_TYPE_INTEGER); }

    public function getModule(): ?string { return $this->module; }
    public function setModule(?string $module): void { $this->setVariable('module', $module, self::VALUE_TYPE_STRING); }

    public function getAction(): ?string { return $this->action; }
    public function setAction(?string $action): void { $this->setVariable('action', $action, self::VALUE_TYPE_STRING); }

    public function getParams(mixed $key = null): mixed { return $this->getJSON('params', $key); }
    public function setParams(mixed $params): void { $this->setVariable('params', $params, self::VALUE_TYPE_JSON); }
}
