<?php

namespace App\Model\Base;

class BaseTranslateEntity extends AEntity
{

    public ?int $entity_id = null;
    public ?int $lang_id = null;
    public ?string $value = null;


    public function setEntityId($id): void
    {
        $this->setVariable('entity_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getEntityId() : ?int
    {
        return $this->entity_id;
    }

    public function setLangId($id): void
    {
        $this->setVariable('lang_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getLangId() : ?int
    {
        return $this->lang_id;
    }

    public function setValue($value) : void
    {
        $this->setVariable('value', $value, self::VALUE_TYPE_STRING);
    }

    public function getValue() : ?string
    {
        return $this->value;
    }
}
