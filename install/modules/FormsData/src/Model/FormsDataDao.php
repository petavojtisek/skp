<?php

namespace App\Modules\FormsData\Model;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class FormsDataDao extends BaseDao
{
    protected string $entityName = 'App\Modules\FormsData\Model\FormsDataEntity';

    /** @var FormsDataMapper */
    protected IMapper $mapper;

    public function __construct(FormsDataMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function findFormsData(int $limit, int $offset, ?string $search = null): array
    {
        $data = $this->mapper->findFormsData($limit, $offset, $search);
        return $this->getEntities($this->entityName, $data);
    }

    public function countFormsData(?string $search = null): int
    {
        return $this->mapper->countFormsData($search);
    }
}
