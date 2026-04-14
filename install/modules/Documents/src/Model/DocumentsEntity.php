<?php

namespace App\Modules\Documents\Model;

use App\Model\Base\BaseEntity;

class DocumentsEntity extends BaseEntity
{
    public mixed $element_id = null;
    public ?string $text = null;
    public ?int $file_id = null;

    // Joined from 'element' table
    public ?string $name = null;
    public ?int $status_id = null;
    public mixed $created_dt = null;

    // Joined from 'file_manager' table (optional, for display)
    public ?string $original_name = null;

    public function getId(): mixed
    {
        return $this->element_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('element_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->setVariable('text', $text, self::VALUE_TYPE_STRING);
    }

    public function getFileId(): ?int
    {
        return $this->file_id;
    }

    public function setFileId(?int $fileId): void
    {
        $this->setVariable('file_id', $fileId, self::VALUE_TYPE_INTEGER);
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

    public function getOriginalName(): ?string
    {
        return $this->original_name;
    }
}
