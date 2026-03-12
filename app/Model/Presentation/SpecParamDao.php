<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseDao;

class SpecParamDao extends BaseDao
{
    protected $entityName = 'Presentation\SpecParamEntity';

    public function __construct(SpecParamMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function findByPresentation(int $presentationId): array
    {
        return $this->findAllBy(['presentation_id' => $presentationId]) ?: [];
    }

    public function getSpecParam(int $id): ?SpecParamEntity
    {
        return $this->find($id) ?: null;
    }

    public function saveSpecParam(SpecParamEntity $entity): int
    {
        return $entity->getId() ? $this->update($entity) : (int) $this->insert($entity);
    }

    public function deleteSpecParam(int $id): void
    {
        $this->delete($id);
    }
}
