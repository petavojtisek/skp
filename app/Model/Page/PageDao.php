<?php

namespace App\Model\Page;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class PageDao extends BaseDao
{
    protected string $entityName = 'Page\\PageEntity';

    /** @var PageMapper */
    protected IMapper $mapper;

    public function __construct(PageMapper $mapper)
    {
        $this->mapper = $mapper;
    }



}
