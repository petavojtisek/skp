<?php

namespace App\Model\Page;

use App\Model\Base\BaseEntity;

class PageEntity extends BaseEntity
{
    protected $id;
    protected $name;
    protected $url;
    protected $title;
    protected $active;
}
