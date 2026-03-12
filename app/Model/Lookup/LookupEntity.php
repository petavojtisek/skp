<?php

namespace App\Model\Lookup;

use App\Model\Base\BaseEntity;

class LookupEntity extends BaseEntity
{
    public $lookup_id;
    public $parent_id;
    public $item;
    public $constant;
    
    /** @var array Překlady [lang_id => item] */
    protected $translations = [];

    public function getId() { return $this->lookup_id; }
    public function setId($id): void { $this->setVariable('lookup_id', $id, self::VALUE_TYPE_INTEGER); }

    public function setTranslations(array $translations): void { $this->translations = $translations; }
    public function getTranslations(): array { return $this->translations; }
}
