<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseDao;

class ComponentActionDao extends BaseDao
{
    protected $entityName = 'Presentation\ComponentActionEntity';

    public function __construct(ComponentActionMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function findByPresentation(int $presentationId): array
    {
        return $this->findAllBy(['presentation_id' => $presentationId]) ?: [];
    }
}
