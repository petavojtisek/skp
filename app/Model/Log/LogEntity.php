<?php

namespace App\Model\Log;

use App\Model\Base\BaseEntity;

class LogEntity extends BaseEntity
{
    public mixed $id = null;
    public mixed $admin_id = null;
    public ?string $module = null;
    public ?string $code_name = null;
    public ?string $action = null;
    public ?string $name = null;
    public mixed $element_id = null;
    public mixed $component_id = null;
    public mixed $after = null;
    public mixed $before = null;
    public ?string $url = null;
    public ?string $ssid = null;

    protected ?string $admin_name = null;

    public function getId(): mixed { return $this->id; }
    public function setId(mixed $id): void { $this->setVariable('id', $id, self::VALUE_TYPE_INTEGER); }

    public function getAdminId(): mixed { return $this->admin_id; }
    public function setAdminId(mixed $admin_id): void { $this->setVariable('admin_id', $admin_id, self::VALUE_TYPE_INTEGER); }

    public function getModule(): ?string { return $this->module; }
    public function setModule(?string $module): void { $this->setVariable('module', $module, self::VALUE_TYPE_STRING); }

    public function getCodeName(): ?string { return $this->code_name; }
    public function setCodeName(?string $code_name): void { $this->setVariable('code_name', $code_name, self::VALUE_TYPE_STRING); }

    public function getAction(): ?string { return $this->action; }
    public function setAction(?string $action): void { $this->setVariable('action', $action, self::VALUE_TYPE_STRING); }

    public function getName(): ?string { return $this->name; }
    public function setName(?string $name): void { $this->setVariable('name', $name, self::VALUE_TYPE_STRING); }

    public function getElementId(): mixed { return $this->element_id; }
    public function setElementId(mixed $element_id): void { $this->setVariable('element_id', $element_id, self::VALUE_TYPE_INTEGER); }

    public function getComponentId(): mixed { return $this->component_id; }
    public function setComponentId(mixed $component_id): void { $this->setVariable('component_id', $component_id, self::VALUE_TYPE_INTEGER); }

    public function getUrl(): ?string { return $this->url; }
    public function setUrl(?string $url): void { $this->setVariable('url', $url, self::VALUE_TYPE_STRING); }

    public function getSsid(): ?string { return $this->ssid; }
    public function setSsid(?string $ssid): void { $this->setVariable('ssid', $ssid, self::VALUE_TYPE_STRING); }

    public function setBefore(mixed $data): void { $this->setVariable('before', $data, self::VALUE_TYPE_JSON); }
    public function setAfter(mixed $data): void { $this->setVariable('after', $data, self::VALUE_TYPE_JSON); }

    public function getBefore(mixed $key = null) : mixed { return $this->getJSON('before', $key); }
    public function getAfter(mixed $key = null) : mixed { return $this->getJSON('after', $key); }

    public function setAdminName(?string $admin_name): void { $this->admin_name = $admin_name; }
    public function getAdminName(): ?string { return $this->admin_name; }
}
