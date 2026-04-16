<?php

namespace App\Modules\Forms\Model;

use App\Model\Base\BaseService;

class FormsService extends BaseService
{
    /** @var FormsDao */
    protected $dao;

    public function __construct(FormsDao $dao)
    {
        $this->dao = $dao;
    }

    public function getByComponentId(int $componentId): array
    {
        return $this->dao->getByComponentId($componentId);
    }

    public function getForm(int $id): ?FormsEntity
    {
        return $this->dao->find($id);
    }

    public function saveForm(FormsEntity $entity): void
    {
        $this->dao->save($entity);
    }

    public function deleteForm(int $id): void
    {
        $this->dao->delete($id);
    }
}
