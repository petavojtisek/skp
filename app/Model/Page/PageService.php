<?php

namespace App\Model\Page;

use App\Model\Base\BaseService;

class PageService extends BaseService
{
    /** @var PageDao */
    private $pageDao;

    public function __construct(PageDao $pageDao)
    {
        $this->pageDao = $pageDao;
    }
}
