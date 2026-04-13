<?php

namespace App\Modules\WebTexts\Model;

use App\Model\Base\AEntity;

class WebTextEntity extends AEntity
{
    public int $web_text_id;
    public ?string $code = null;
    public ?string $text = null;

    public function getWebTextId(): int
    {
        return $this->web_text_id;
    }

    public function getId() :mixed
    {
        return $this->web_text_id;
    }

    public function setId($id) : void
    {
        $this->web_text_id =$id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->setVariable('code',$code, self::VALUE_TYPE_STRING);
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->setVariable('text',$text, self::VALUE_TYPE_STRING);
    }
}
