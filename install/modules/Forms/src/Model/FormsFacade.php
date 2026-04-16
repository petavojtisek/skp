<?php

namespace App\Modules\Forms\Model;

use App\Model\Base\BaseFacade;

class FormsFacade extends BaseFacade
{
    /** @var FormsService */
    protected $service;

    public function __construct(FormsService $service)
    {
        $this->service = $service;
    }

    public function findForms(int $limit, int $offset, ?string $search = null): array
    {
        return $this->service->findForms($limit, $offset, $search);
    }

    public function countForms(?string $search = null): int
    {
        return $this->service->countForms($search);
    }

    public function getForm(int $id): ?FormsEntity
    {
        return $this->service->getForm($id);
    }

    public function deleteForm(int $id): void
    {
        $this->service->deleteForm($id);
    }

    public function saveForm(FormsEntity $entity): void
    {
        $this->service->saveForm($entity);
    }
}
