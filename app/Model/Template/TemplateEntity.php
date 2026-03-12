<?php

namespace App\Model\Template;

use App\Model\Base\BaseEntity;

class TemplateEntity extends BaseEntity
{
    public $template_id;
    public $template_type;
    public $template_filename;
    public $template_name;
    public $template_path;
    public $presentation_id;

    public function getId() { return $this->template_id; }
    public function setId($id): void { $this->setVariable('template_id', $id, self::VALUE_TYPE_INTEGER); }
}
