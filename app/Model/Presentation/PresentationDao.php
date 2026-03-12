<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseDao;

class PresentationDao extends BaseDao
{
    protected string $entityName = 'Presentation\\PresentationEntity';

    /** @var PresentationMapper */
    protected $mapper;

    public function __construct(PresentationMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return PresentationMapper
     */
    public function getMapper(): \App\Model\Base\IMapper
    {
        return $this->mapper;
    }
}
