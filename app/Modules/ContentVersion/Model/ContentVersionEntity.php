<?php

namespace App\Modules\ContentVersion\Model;

use App\Model\Base\BaseEntity;

class ContentVersionEntity extends BaseEntity
{
    public mixed $element_id = null;
    public ?string $content = null;

    // Joined from 'element' table
    public ?string $name = null;
    public ?int $status_id = null;
    public mixed $created_dt = null;

    public function getId(): mixed
    {
        return $this->element_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('element_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getContent(): ?string
    {

        $clean = $this->content;
        if(!empty($this->content)){
            $clean = html_entity_decode($this->content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Odstraň i ty zbloudilé &nbsp; které tam TinyMCE sype (pokud chceš)
            $clean = str_replace("\xc2\xa0", ' ', $clean);
        }

        return $clean;
    }

    public function setContent(?string $content): void
    {
        $this->setVariable('content', $content, self::VALUE_TYPE_STRING);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStatus(): ?int
    {
        return $this->status_id;
    }

    public function getCreatedDt($format = null)
    {
        return $this->getDateTime($this->created_dt, $format);
    }
}
