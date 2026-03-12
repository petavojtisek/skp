<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseEntity;

class PresentationEntity extends BaseEntity
{
    /** @var int */
    public $presentation_id;

    /** @var int */
    public $presentation_lang;

    /** @var int */
    public $presentation_status;

    /** @var string|null */
    public $presentation_name;

    /** @var string|null */
    public $domain;

    /** @var string */
    public $directory;

    /** @var string|null */
    public $presentation_description;

    /** @var string|null */
    public $presentation_keywords;

    /** @var int */
    public $is_default = 0;

    public function getId()
    {
        return $this->presentation_id;
    }

    public function setId($id): void
    {
        $this->setVariable('presentation_id', $id, self::VALUE_TYPE_INTEGER);
    }
}
