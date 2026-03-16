<?php

namespace App\Model\Page;

use App\Model\Base\BaseEntity;

class PageEntity extends BaseEntity
{
    public mixed $page_id = null;
    public ?int $page_parent_id = 0;
    public ?int $presentation_id = null;
    public ?int $page_status = null;
    public ?int $position = null;
    public ?int $template_id = null;
    public ?string $page_name = null;
    public ?string $page_description = null;
    public ?string $page_keywords = null;
    public ?string $page_title = null;
    public ?string $page_rewrite = null;
    public ?string $page_redirect = null;
    public ?int $page_redirect_id = null;
    public ?string $page_sitemap = 'N';
    public ?string $page_menu = 'N';
    public ?string $restricted_area = 'N';

    public function getId(): mixed
    {
        return $this->page_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('page_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getPageParentId(): ?int
    {
        return $this->page_parent_id;
    }

    public function setPageParentId(?int $page_parent_id): void
    {
        $this->setVariable('page_parent_id', $page_parent_id, self::VALUE_TYPE_INTEGER);
    }

    public function getPresentationId(): ?int
    {
        return $this->presentation_id;
    }

    public function setPresentationId(?int $presentation_id): void
    {
        $this->setVariable('presentation_id', $presentation_id, self::VALUE_TYPE_INTEGER);
    }

    public function getPageStatus(): ?int
    {
        return $this->page_status;
    }

    public function setPageStatus(?int $page_status): void
    {
        $this->setVariable('page_status', $page_status, self::VALUE_TYPE_INTEGER);
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->setVariable('position', $position, self::VALUE_TYPE_INTEGER);
    }

    public function getTemplateId(): ?int
    {
        return $this->template_id;
    }

    public function setTemplateId(?int $template_id): void
    {
        $this->setVariable('template_id', $template_id, self::VALUE_TYPE_INTEGER);
    }

    public function getPageName(): ?string
    {
        return $this->page_name;
    }

    public function setPageName(?string $page_name): void
    {
        $this->setVariable('page_name', $page_name, self::VALUE_TYPE_STRING);
    }

    public function getPageDescription(): ?string
    {
        return $this->page_description;
    }

    public function setPageDescription(?string $page_description): void
    {
        $this->setVariable('page_description', $page_description, self::VALUE_TYPE_STRING);
    }

    public function getPageKeywords(): ?string
    {
        return $this->page_keywords;
    }

    public function setPageKeywords(?string $page_keywords): void
    {
        $this->setVariable('page_keywords', $page_keywords, self::VALUE_TYPE_STRING);
    }

    public function getPageTitle(): ?string
    {
        return $this->page_title;
    }

    public function setPageTitle(?string $page_title): void
    {
        $this->setVariable('page_title', $page_title, self::VALUE_TYPE_STRING);
    }

    public function getPageRewrite(): ?string
    {
        return $this->page_rewrite;
    }

    public function setPageRewrite(?string $page_rewrite): void
    {
        $this->setVariable('page_rewrite', $page_rewrite, self::VALUE_TYPE_STRING);
    }

    public function getPageRedirect(): ?string
    {
        return $this->page_redirect;
    }

    public function setPageRedirect(?string $page_redirect): void
    {
        $this->setVariable('page_redirect', $page_redirect, self::VALUE_TYPE_STRING);
    }

    public function getPageRedirectId(): ?int
    {
        return $this->page_redirect_id;
    }

    public function setPageRedirectId(?int $page_redirect_id): void
    {
        $this->setVariable('page_redirect_id', $page_redirect_id, self::VALUE_TYPE_INTEGER);
    }

    public function getPageSitemap(): ?string
    {
        return $this->page_sitemap;
    }

    public function setPageSitemap(?string $page_sitemap): void
    {
        $this->setVariable('page_sitemap', $page_sitemap, self::VALUE_TYPE_STRING);
    }

    public function getPageMenu(): ?string
    {
        return $this->page_menu;
    }

    public function setPageMenu(?string $page_menu): void
    {
        $this->setVariable('page_menu', $page_menu, self::VALUE_TYPE_STRING);
    }

    public function getRestrictedArea(): ?string
    {
        return $this->restricted_area;
    }

    public function setRestrictedArea(?string $restricted_area): void
    {
        $this->setVariable('restricted_area', $restricted_area, self::VALUE_TYPE_STRING);
    }
}
