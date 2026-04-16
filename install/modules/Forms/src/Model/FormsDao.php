<?php

namespace App\Modules\Forms\Model;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class FormsDao extends BaseDao
{
    protected string $entityName = 'App\Modules\Forms\Model\FormsEntity';

    /** @var FormsMapper */
    protected IMapper $mapper;

    public function __construct(FormsMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function findForms(int $limit, int $offset, ?string $search = null): array
    {
        $data = $this->mapper->findForms($limit, $offset, $search);
        return $this->getEntities($this->entityName, $data);
    }

    public function countForms(?string $search = null): int
    {
        return $this->mapper->countForms($search);
    }
}
