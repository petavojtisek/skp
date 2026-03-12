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
}
