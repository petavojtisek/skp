<?php

namespace App\Model\Entity;

use App\Model\Base\BaseEntity;

class CodeNameEntity extends BaseEntity
{
    public mixed $id = null;
    public mixed $template_id = null;
    public ?int $module = null;
    public ?string $code_name = null;

    public function getId(): mixed { return $this->id; }
    public function setId(mixed $id): void { $this->setVariable('id', $id, self::VALUE_TYPE_INTEGER); }

    public function getTemplateId(): mixed { return $this->template_id; }
    public function setTemplateId(mixed $template_id): void { $this->setVariable('template_id', $template_id, self::VALUE_TYPE_INTEGER); }

    public function getModule(): ?int { return $this->module; }
    public function setModule(?int $module): void { $this->setVariable('module', $module, self::VALUE_TYPE_INTEGER); }

    public function getCodeName(): ?string { return $this->code_name; }
    public function setCodeName(?string $code_name): void { $this->setVariable('code_name', $code_name, self::VALUE_TYPE_STRING); }
}
