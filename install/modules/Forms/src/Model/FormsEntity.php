<?php

namespace App\Modules\Forms\Model;

use App\Model\Base\BaseEntity;

class FormsEntity extends BaseEntity
{
    public mixed $element_id = null;
    public ?string $form_component = null;

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

    public function getFormComponent(): ?string
    {
        return $this->form_component;
    }

    public function setFormComponent(?string $form_component): void
    {
        $this->setVariable('form_component', $form_component, self::VALUE_TYPE_STRING);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getStatusId(): ?int
    {
        return $this->status_id;
    }

    public function setStatusId(?int $status_id): void
    {
        $this->setVariable('status_id', $status_id, self::VALUE_TYPE_INTEGER);
    }

    public function getCreatedDt($format = null)
    {
        return $this->getDateTime($this->created_dt, $format);
    }
 
}
