<?php

namespace App\Model\Element;

use App\Model\Base\BaseEntity;

class ElementEntity extends BaseEntity
{
    public mixed $element_id = null;
    public ?int $component_id = null;
    public ?string $name = null;
    public ?int $status_id = null;
    public ?int $author_id = null;
    public mixed $valid_from = null;
    public mixed $valid_to = null;
    public mixed $inserted = null;

    public function getId(): mixed
    {
        return $this->element_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('element_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getComponentId(): ?int
    {
        return $this->component_id;
    }

    public function setComponentId(?int $id): void
    {
        $this->setVariable('component_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->setVariable('name', $name, self::VALUE_TYPE_STRING);
    }

    public function getStatusId(): ?int
    {
        return $this->status_id;
    }

    public function setStatusId(?int $id): void
    {
        $this->setVariable('status_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getAuthorId(): ?int
    {
        return $this->author_id;
    }

    public function setAuthorId(?int $id): void
    {
        $this->setVariable('author_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getValidFrom($format = null)
    {
        return $this->getDateTime($this->valid_from, $format);
    }

    public function setValidFrom(mixed $date): void
    {
        $this->setVariable('valid_from', $date, self::VALUE_TYPE_DATE);
    }

    public function getValidTo($format = null)
    {
        return $this->getDateTime($this->valid_to, $format);
    }

    public function setValidTo(mixed $date): void
    {
        $this->setVariable('valid_to', $date, self::VALUE_TYPE_DATE);
    }

    public function getInserted($format = null)
    {
        return $this->getDateTime($this->inserted, $format);
    }


}
