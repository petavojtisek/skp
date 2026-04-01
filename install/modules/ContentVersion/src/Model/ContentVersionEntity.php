<?php

namespace App\Modules\ContentVersion\Model;

use App\Model\Base\BaseEntity;

class ContentVersionEntity extends BaseEntity
{
    public mixed $element_id = null;
    public ?string $content = null;



    public function getId(): mixed
    {
        return $this->element_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('element_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->setVariable('content', $content, self::VALUE_TYPE_STRING);
    }

}
