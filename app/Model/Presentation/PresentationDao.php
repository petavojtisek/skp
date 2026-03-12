<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseDao;

class PresentationDao extends BaseDao
{
    protected $entityName = 'Presentation\PresentationEntity';

    public function __construct(PresentationMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
