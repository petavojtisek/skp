<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseEntity;

class PresentationEntity extends BaseEntity
{
    public mixed $presentation_id = null;
    public mixed $presentation_lang = null;
    public mixed $presentation_status = null;
    public ?string $presentation_name = null;
    public ?string $domain = null;
    public ?string $directory = null;
    public ?string $presentation_description = null;
    public ?string $presentation_keywords = null;
    public mixed $is_default = 0;

    public function getId(): mixed
    {
        return $this->presentation_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('presentation_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getPresentationLang(): mixed { return $this->presentation_lang; }
    public function setPresentationLang(mixed $lang): void { $this->setVariable('presentation_lang', $lang, self::VALUE_TYPE_INTEGER); }

    public function getPresentationStatus(): mixed { return $this->presentation_status; }
    public function setPresentationStatus(mixed $status): void { $this->setVariable('presentation_status', $status, self::VALUE_TYPE_INTEGER); }

    public function getPresentationName(): ?string { return $this->presentation_name; }
    public function setPresentationName(?string $name): void { $this->setVariable('presentation_name', $name, self::VALUE_TYPE_STRING); }

    public function getDomain(): ?string { return $this->domain; }
    public function setDomain(?string $domain): void { $this->setVariable('domain', $domain, self::VALUE_TYPE_STRING); }

    public function getDirectory(): ?string { return $this->directory; }
    public function setDirectory(?string $directory): void { $this->setVariable('directory', $directory, self::VALUE_TYPE_STRING); }

    public function getPresentationDescription(): ?string { return $this->presentation_description; }
    public function setPresentationDescription(?string $description): void { $this->setVariable('presentation_description', $description, self::VALUE_TYPE_STRING); }

    public function getPresentationKeywords(): ?string { return $this->presentation_keywords; }
    public function setPresentationKeywords(?string $keywords): void { $this->setVariable('presentation_keywords', $keywords, self::VALUE_TYPE_STRING); }

    public function getIsDefault(): mixed { return $this->is_default; }
    public function setIsDefault(mixed $is_default): void { $this->setVariable('is_default', $is_default, self::VALUE_TYPE_INTEGER); }
}
