<?php

namespace App\Modules\News\Model;

use App\Model\Base\BaseEntity;

class NewsEntity extends BaseEntity
{

    const string WEB_STORAGE = "/web_storage/news/",
                 IMG_THUMB = "thumb",
                 IMG_ORIG = "orig",
                 IMG_SIZE = "800x600";



    public mixed $element_id = null;
    public ?string $title = null;
    public ?string $short_text = null;
    public ?string $content = null;
    public ?string $image = null;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->setVariable('title', $title, self::VALUE_TYPE_STRING);
    }

    public function getShortText(): ?string
    {
        return $this->short_text;
    }

    public function setShortText(?string $shortText): void
    {
        $this->setVariable('short_text', $shortText, self::VALUE_TYPE_STRING);
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->setVariable('content', $content, self::VALUE_TYPE_STRING);
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->setVariable('image', $image, self::VALUE_TYPE_STRING);
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

    public function getImagePath($dir = 'thumb')
    {
        if($image = $this->getImage()) {
            return self::WEB_STORAGE .  $dir .DS. $image;
        }
        return null;
    }
}
