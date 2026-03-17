<?php

namespace App\Model\Admin;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class AdminDao extends BaseDao
{
    protected string $entityName = 'Admin\AdministratorEntity';

    /** @var AdminMapper */
    protected IMapper $mapper;

    public function __construct(AdminMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getActiveAdmins(?array $groupIds = null): array
    {
        $data = $this->mapper->getActiveAdmins($groupIds);
        return $this->getEntities($this->entityName, $data);
    }

    /**
     * @return AdminMapper
     */
    public function getMapper(): IMapper
    {
        return $this->mapper;
    }
}
