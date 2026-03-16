<?php

namespace App\Model\ModuleRightsOld;

use App\Model\Base\BaseEntity;

class ModuleRightsOldEntity extends BaseEntity
{
    public mixed $module_rights_id = null;

    public function getId(): mixed { return $this->module_rights_id; }
    public function setId(mixed $id): void { $this->setVariable('module_rights_id', $id, self::VALUE_TYPE_INTEGER); }
}
