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

    public function getByComponentId(int $componentId): array
    {
        return $this->service->getByComponentId($componentId);
    }

    public function getActiveElementId(int $componentId): ?int
    {
        return $this->service->getActiveElementId($componentId);
    }

    public function getForm(int $id): ?FormsEntity
    {
        return $this->service->getForm($id);
    }

    public function saveForm(FormsEntity $entity): void
    {
        $this->service->saveForm($entity);
    }

    public function deleteForm(int $id): void
    {
        $this->service->deleteForm($id);
    }
}
