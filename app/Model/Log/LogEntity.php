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
    public mixed $send_data = null;
    public mixed $before_data = null;
    public mixed $inserted = null;

    public function getId(): mixed { return $this->id; }
    public function setId(mixed $id): void { $this->setVariable('id', $id, self::VALUE_TYPE_INTEGER); }

    public function setSendData(mixed $data): void { $this->setVariable('send_data', $data, self::VALUE_TYPE_JSON); }
    public function getSend_data(mixed $format = null): mixed { return $this->getJSON('send_data', $format); }

    public function setBeforeData(mixed $data): void { $this->setVariable('before_data', $data, self::VALUE_TYPE_JSON); }
    public function getBefore_data(mixed $format = null): mixed { return $this->getJSON('before_data', $format); }

    public function setInserted(mixed $inserted): void { $this->setVariable('inserted', $inserted, self::VALUE_TYPE_DATE); }
}
