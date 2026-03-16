<?php

namespace App\Model\AdminRight;

use App\Model\Base\BaseService;

class AdminRightService extends BaseService
{
    private AdminRightDao $adminRightDao;

    public function __construct(AdminRightDao $adminRightDao)
    {
        $this->adminRightDao = $adminRightDao;
    }

    public function findAll(): array
    {
        return $this->adminRightDao->findAll() ?: [];
    }

    public function find(int $id): ?AdminRightEntity
    {
        return $this->adminRightDao->find($id) ?: null;
    }

    public function save(AdminRightEntity $entity): int
    {
        return (int)$this->adminRightDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->adminRightDao->delete($id);
    }
}
