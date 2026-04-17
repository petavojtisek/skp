<?php

namespace App\Modules\SystemConstants\Model;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class SystemConstantsDao extends BaseDao
{
    protected string $entityName = 'App\Modules\SystemConstants\Model\SystemConstantsEntity';

    /** @var SystemConstantsMapper */
    protected IMapper $mapper;

    public function __construct(SystemConstantsMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function findSystemConstants(?string $search = null, int $limit = 10, int $offset = 0): array
    {
        $data = $this->mapper->findSystemConstants($search, $limit, $offset);
        return $this->getEntities($this->entityName, $data);
    }

    public function countSystemConstants(?string $search = null): int
    {
        return $this->mapper->countSystemConstants($search);
    }

    public function getAllSystemConstants() : array
    {
        $data = $this->mapper->findAll();
        return $this->getEntities($this->entityName, $data);
    }
}
