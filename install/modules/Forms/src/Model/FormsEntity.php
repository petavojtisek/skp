<?php

namespace App\Modules\Forms\Model;

use App\Model\Base\BaseEntity;

class FormsEntity extends BaseEntity
{
    public ?int $id = null;
    public ?string $form_name = null;
    public mixed $data = null;
    public ?string $ip_address = null;
    public mixed $created_dt = null;
    public ?int $status = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getFormName(): ?string
    {
        return $this->form_name;
    }

    public function setFormName(?string $formName): void
    {
        $this->setVariable('form_name', $formName, self::VALUE_TYPE_STRING);
    }

    public function getData(): mixed
    {
        if (is_string($this->data)) {
            return json_decode($this->data, true);
        }
        return $this->data;
    }

    public function setData(mixed $data): void
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        $this->setVariable('data', $data, self::VALUE_TYPE_STRING);
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(?string $ipAddress): void
    {
        $this->setVariable('ip_address', $ipAddress, self::VALUE_TYPE_STRING);
    }

    public function getCreatedDt($format = null)
    {
        return $this->getDateTime($this->created_dt, $format);
    }

    public function setCreatedDt(mixed $createdDt): void
    {
        $this->setVariable('created_dt', $createdDt, self::VALUE_TYPE_DATETIME);
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(mixed $status): void
    {
        $this->setVariable('status', $status, self::VALUE_TYPE_INTEGER);
    }
}
