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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }
}
