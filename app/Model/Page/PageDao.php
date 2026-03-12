<?php

namespace App\Model\Page;

use App\Model\Base\BaseDao;

class PageDao extends BaseDao
{
    protected string $entityName = 'Page\\PageEntity';

    /** @var PageMapper */
    protected $mapper;

    public function __construct(PageMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return PageMapper
     */
    public function getMapper(): \App\Model\Base\IMapper
    {
        return $this->mapper;
    }
}
