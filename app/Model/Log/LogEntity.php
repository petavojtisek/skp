<?php

namespace App\Model\Log;

use App\Model\Base\BaseEntity;

class LogEntity extends BaseEntity
{
    public $id;
    public $admin_id;
    public $module;
    public $code_name;
    public $action;
    public $name;
    public $element_id;
    public $component_id;
    public $send_data;
    public $before_data;
    public $inserted;

    public function getId() { return $this->id; }
    public function setId($id): void { $this->setVariable('id', $id, self::VALUE_TYPE_INTEGER); }

    public function setSendData($data): void { $this->setVariable('send_data', $data, self::VALUE_TYPE_JSON); }
    public function getSend_data($format = null) { return $this->getJSON('send_data', $format); }

    public function setBeforeData($data): void { $this->setVariable('before_data', $data, self::VALUE_TYPE_JSON); }
    public function getBefore_data($format = null) { return $this->getJSON('before_data', $format); }

    public function setInserted($inserted): void { $this->setVariable('inserted', $inserted, self::VALUE_TYPE_DATE); }
}
