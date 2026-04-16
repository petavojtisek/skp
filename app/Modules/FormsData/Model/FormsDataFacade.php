<?php

namespace App\Modules\FormsData\Model;


class FormsDataFacade
{
    /** @var FormsDataService */
    protected $service;

    public function __construct(FormsDataService $service)
    {
        $this->service = $service;
    }

    public function findFormsData(int $limit, int $offset, ?string $search = null): array
    {
        return $this->service->findFormsData($limit, $offset, $search);
    }

    public function countFormsData(?string $search = null): int
    {
        return $this->service->countFormsData($search);
    }

    public function getFormData(int $id): ?FormsDataEntity
    {
        return $this->service->getFormData($id);
    }

    public function deleteFormData(int $id): void
    {
        $this->service->deleteFormData($id);
    }

    public function saveFormData(FormsDataEntity $entity): void
    {
        $this->service->saveFormData($entity);
    }
}
