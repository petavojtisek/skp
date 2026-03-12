<?php

namespace App\Model\Page;

use App\Model\Base\BaseDao;

class PageDao extends BaseDao
{
    protected $entityName = 'Page\PageEntity';

    public function __construct(PageMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
