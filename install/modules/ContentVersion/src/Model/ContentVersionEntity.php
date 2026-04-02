<?php

namespace App\Modules\ContentVersion\Model;

use App\Model\Base\BaseEntity;

class ContentVersionEntity extends BaseEntity
{
    public mixed $element_id = null;
    public ?string $content = null;

    // Joined from 'element' table
    public ?string $name = null;
    public ?int $status_id = null;
    public mixed $created_dt = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStatus(): ?int
    {
        return $this->status_id;
    }

    public function getCreatedDt($format = null)
    {
        return $this->getDateTime($this->created_dt, $format);
    }

    public function getActive(): int
    {
        return $this->status_id == 1 ? 1 : 0; // Temporary logic for 'Aktivní' checkbox
    }
}
