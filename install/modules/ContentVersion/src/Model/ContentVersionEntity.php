<?php

namespace App\Modules\ContentVersion\Model;

use App\Model\Base\BaseEntity;

class ContentVersionEntity extends BaseEntity
{
    public mixed $element_id = null;
    public ?string $content = null;
    public ?string $name = null;
    public ?int $status = null;
    public ?int $active = null;
    public ?string $created_dt = null;

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

    public function setName(?string $name): void
    {
        $this->setVariable('name', $name, self::VALUE_TYPE_STRING);
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): void
    {
        $this->setVariable('status', $status, self::VALUE_TYPE_INTEGER);
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(?int $active): void
    {
        $this->setVariable('active', $active, self::VALUE_TYPE_INTEGER);
    }

    public function getCreatedDt(): ?string
    {
        return $this->created_dt;
    }

    public function setCreatedDt(?string $created_dt): void
    {
        $this->setVariable('created_dt', $created_dt, self::VALUE_TYPE_STRING);
    }
}
