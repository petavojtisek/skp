<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseDao;
use App\Model\Base\BaseMapper;
use App\Model\Base\IMapper;


class PresentationDao extends BaseDao
{
    protected string $entityName = 'Presentation\\PresentationEntity';


    /** @var PresentationMapper */
    protected IMapper $mapper;

    public function __construct(PresentationMapper $mapper)

    {
        $this->mapper = $mapper;
    }


    public function getAdminPresentations(int $adminId): array
    {
        return $this->mapper->getAdminPresentations($adminId);
    }

    public function saveAdminPresentations(int $adminId, array $presentationIds): void
    {
        $this->mapper->saveAdminPresentations($adminId, $presentationIds);
    }
}
