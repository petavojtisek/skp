<?php

namespace App\Model\Version;

use App\Model\Base\BaseEntity;

class VersionEntity extends BaseEntity
{
    public ?int $component_id = null;
    public ?int $element_id = null;

    public function getComponentId(): ?int
    {
        return $this->component_id;
    }

    public function setComponentId(?int $id): void
    {
        $this->setVariable('component_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getElementId(): ?int
    {
        return $this->element_id;
    }

    public function setElementId(?int $id): void
    {
        $this->setVariable('element_id', $id, self::VALUE_TYPE_INTEGER);
    }
}
