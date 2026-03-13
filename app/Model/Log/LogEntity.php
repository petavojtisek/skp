<?php

namespace App\Model\Log;

use App\Model\Base\BaseEntity;

class LogEntity extends BaseEntity
{
    public mixed $id = null;
    public mixed $admin_id = null;
    public ?string $module = null;
    public ?string $code_name = null;
    public ?string $action = null;
    public ?string $name = null;
    public mixed $element_id = null;
    public mixed $component_id = null;
    public mixed $after = null;
    public mixed $before = null;
    public mixed $inserted = null;



    protected ?string $admin_name;


    public function setAdminName($admin_name): void
    {
        $this->admin_name = $admin_name;
    }

    public function getAdminName(): ?string
    {
        return $this->admin_name;
    }


    public function getId(): mixed { return $this->id; }
    public function setId(mixed $id): void { $this->setVariable('id', $id, self::VALUE_TYPE_INTEGER); }

    public function setBefore(mixed $data): void { $this->setVariable('before', $data, self::VALUE_TYPE_JSON); }
    public function setAfter(mixed $data): void { $this->setVariable('after', $data, self::VALUE_TYPE_JSON); }

    public function getBefore(mixed $key = null) : mixed
    {
        return $this->getJSON('before', $key);
    }

    public function getAfter(mixed $key = null) : mixed
    {
        return $this->getJSON('after', $key);
    }



}
