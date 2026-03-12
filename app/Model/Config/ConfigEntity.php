<?php

namespace App\Model\Config;

use App\Model\Base\BaseEntity;

class ConfigEntity extends BaseEntity
{
    public $config_id;
    public $item;
    public $value;
    
    /** @var array Multilingual values [lang_id => value] */
    protected $translations = [];

    public function getId() { return $this->config_id; }
    public function setId($id): void { $this->setVariable('config_id', $id, self::VALUE_TYPE_INTEGER); }

    public function setTranslations(array $translations): void { $this->translations = $translations; }
    public function getTranslations(): array { return $this->translations; }
}
