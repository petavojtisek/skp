<?php

namespace App\Model\Config;

use App\Model\Base\BaseEntity;

class ConfigEntity extends BaseEntity
{
    public mixed $config_id = null;
    public ?string $item = null;
    public ?string $value = null;


    public function getId(): mixed { return $this->config_id; }
    public function setId(mixed $id): void { $this->setVariable('config_id', $id, self::VALUE_TYPE_INTEGER); }


}
