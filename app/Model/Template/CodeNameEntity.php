<?php

namespace App\Model\Template;

use App\Model\Base\BaseEntity;

class CodeNameEntity extends BaseEntity
{
    public $id;
    public $template_id;
    public $module;
    public $code_name;

    public function getId() { return $this->id; }
    public function setId($id): void { $this->setVariable('id', $id, self::VALUE_TYPE_INTEGER); }
}
