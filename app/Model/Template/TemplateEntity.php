<?php

namespace App\Model\Template;

use App\Model\Base\BaseEntity;

class TemplateEntity extends BaseEntity
{
    public mixed $template_id = null;
    public mixed $template_type = null;
    public ?string $template_filename = null;
    public ?string $template_name = null;
    public ?string $template_path = null;
    public mixed $presentation_id = null;

    public function getId(): mixed { return $this->template_id; }
    public function setId(mixed $id): void { $this->setVariable('template_id', $id, self::VALUE_TYPE_INTEGER); }

    public function getTemplateType(): mixed { return $this->template_type; }
    public function setTemplateType(mixed $type): void { $this->setVariable('template_type', $type, self::VALUE_TYPE_INTEGER); }

    public function getTemplateFilename(): ?string { return $this->template_filename; }
    public function setTemplateFilename(?string $filename): void { $this->setVariable('template_filename', $filename, self::VALUE_TYPE_STRING); }

    public function getTemplateName(): ?string { return $this->template_name; }
    public function setTemplateName(?string $name): void { $this->setVariable('template_name', $name, self::VALUE_TYPE_STRING); }

    public function getTemplatePath(): ?string { return $this->template_path; }
    public function setTemplatePath(?string $path): void { $this->setVariable('template_path', $path, self::VALUE_TYPE_STRING); }

    public function getPresentationId(): mixed { return $this->presentation_id; }
    public function setPresentationId(mixed $presentation_id): void { $this->setVariable('presentation_id', $presentation_id, self::VALUE_TYPE_INTEGER); }
}
