<?php

namespace App\Modules\SystemConstants\Model;

use App\Model\Base\BaseFacade;

class SystemConstantsFacade extends BaseFacade
{
    /** @var SystemConstantsService */
    protected $service;

    public function __construct(SystemConstantsService $service)
    {
        $this->service = $service;
    }

    public function findSystemConstants(?string $search = null, int $limit = 10, int $offset = 0): array
    {
        return $this->service->findSystemConstants($search, $limit, $offset);
    }

    public function countSystemConstants(?string $search = null): int
    {
        return $this->service->countSystemConstants($search);
    }

    public function getSystemConstant(int $id): ?SystemConstantsEntity
    {
        return $this->service->getSystemConstant($id);
    }

    /**
     * @return SystemConstantsService
     */
    public function getAllSystemConstants() : array
    {
        return $this->service->getAllSystemConstants();
    }

    public function saveSystemConstant(SystemConstantsEntity $entity): void
    {
        $this->service->saveSystemConstant($entity);
    }

    public function deleteSystemConstant(int $id): void
    {
        $this->service->deleteSystemConstant($id);
    }
}
