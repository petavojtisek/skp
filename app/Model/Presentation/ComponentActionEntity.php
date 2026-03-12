<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseEntity;

class ComponentActionEntity extends BaseEntity
{
    public $element_id;
    public $presentation_id;
    public $component_id;
    public $module;
    public $action;
    public $params;

    public function getId() { return $this->element_id; }
    public function setId($id): void { $this->setVariable('element_id', $id, self::VALUE_TYPE_INTEGER); }
}
