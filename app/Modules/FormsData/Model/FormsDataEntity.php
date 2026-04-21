<?php

namespace App\Modules\FormsData\Model;

use App\Model\Base\BaseEntity;

class FormsDataEntity extends BaseEntity
{
    public ?int $id = null;
    public ?string $form_name = null;
    public mixed $data = null;
    public mixed $response = null;
    public ?string $ip_address = null;
    public mixed $created_dt = null;
    public mixed $response_dt = null;
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

    public function getData(mixed $key = false): mixed
    {
        return  $this->getJSON('data',$key);
    }

    public function setData(mixed $data): void
    {
        $this->setVariable('data', $data, self::VALUE_TYPE_JSON);
    }

    public function getResponse(mixed $key = false): mixed
    {
        return $this->getJSON('response', $key);
    }

    public function setResponse(mixed $response): void
    {
        $this->setVariable('response', $response, self::VALUE_TYPE_JSON);
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

    public function setCreatedDt($createdDt = null): void
    {
        $this->setVariable('created_dt', $createdDt, self::VALUE_TYPE_DATE);
    }

    public function getResponseDt($format = null)
    {
        return $this->getDateTime($this->response_dt, $format);
    }

    public function setResponseDt($responseDt = null): void
    {
        $this->setVariable('response_dt', $responseDt, self::VALUE_TYPE_DATE);
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
