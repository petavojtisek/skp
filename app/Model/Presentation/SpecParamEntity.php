<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseEntity;

class SpecParamEntity extends BaseEntity
{
    /** @var int */
    public $spec_param_id;

    /** @var int */
    public $presentation_id;

    /** @var string */
    public $name;

    /** @var string */
    public $value;

    public function getId()
    {
        return $this->spec_param_id;
    }

    public function setId($id): void
    {
        $this->setVariable('spec_param_id', $id, self::VALUE_TYPE_INTEGER);
    }
}
