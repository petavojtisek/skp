<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseEntity;

class PresentationEntity extends BaseEntity
{
    public mixed $presentation_id = null;
    public mixed $presentation_lang = null;
    public mixed $presentation_status = null;
    public ?string $presentation_name = null;
    public ?string $domain = null;
    public ?string $directory = null;
    public ?string $presentation_description = null;
    public ?string $presentation_keywords = null;
    public mixed $is_default = 0;

    public function getId(): mixed
    {
        return $this->presentation_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('presentation_id', $id, self::VALUE_TYPE_INTEGER);
    }
}
