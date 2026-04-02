<?php

namespace App\Model\FileManager;

use App\Model\Base\BaseEntity;

class FileManagerEntity extends BaseEntity
{
    public mixed $file_id = null;
    public ?int $element_id = null;
    public ?string $source_type = null;
    public ?string $file_type = null;
    public ?string $original_name = null;
    public ?string $file_name = null;
    public ?string $path = null;
    public ?string $extension = null;
    public ?string $mime_type = null;
    public ?int $size = null;
    public mixed $created_dt = null;
    public ?int $admin_id = null;

    public function getId(): mixed
    {
        return $this->file_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('file_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getFileManagerId(): mixed
    {
        return $this->file_id;
    }

    public function setFileManagerId(mixed $file_id): void
    {
        $this->setVariable('file_id', $file_id, self::VALUE_TYPE_INTEGER);
    }

    public function getElementId(): ?int
    {
        return $this->element_id;
    }

    public function setElementId(?int $element_id): void
    {
        $this->setVariable('element_id', $element_id, self::VALUE_TYPE_INTEGER);
    }

    public function getSourceType(): ?string
    {
        return $this->source_type;
    }

    public function setSourceType(?string $source_type): void
    {
        $this->setVariable('source_type', $source_type, self::VALUE_TYPE_STRING);
    }

    public function getFileType(): ?string
    {
        return $this->file_type;
    }

    public function setFileType(?string $file_type): void
    {
        $this->setVariable('file_type', $file_type, self::VALUE_TYPE_STRING);
    }

    public function getOriginalName(): ?string
    {
        return $this->original_name;
    }

    public function setOriginalName(?string $original_name): void
    {
        $this->setVariable('original_name', $original_name, self::VALUE_TYPE_STRING);
    }

    public function getFileName(): ?string
    {
        return $this->file_name;
    }

    public function setFileName(?string $file_name): void
    {
        $this->setVariable('file_name', $file_name, self::VALUE_TYPE_STRING);
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->setVariable('path', $path, self::VALUE_TYPE_STRING);
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): void
    {
        $this->setVariable('extension', $extension, self::VALUE_TYPE_STRING);
    }

    public function getMimeType(): ?string
    {
        return $this->mime_type;
    }

    public function setMimeType(?string $mime_type): void
    {
        $this->setVariable('mime_type', $mime_type, self::VALUE_TYPE_STRING);
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->setVariable('size', $size, self::VALUE_TYPE_INTEGER);
    }

    public function getCreatedDt($format = null)
    {
        return $this->getDateTime($this->created_dt, $format);
    }

    public function setCreatedDt($createdDt = null): void
    {
        $this->setVariable('created_dt', $createdDt, self::VALUE_TYPE_DATE);
    }

    public function getAdminId(): ?int
    {
        return $this->admin_id;
    }

    public function setAdminId(?int $admin_id): void
    {
        $this->setVariable('admin_id', $admin_id, self::VALUE_TYPE_INTEGER);
    }
}
