<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseService;

class PresentationService extends BaseService
{
    /** @var PresentationDao */
    private $presentationDao;

    /** @var SpecParamDao */
    private $specParamDao;

    /** @var ComponentActionDao */
    private $componentActionDao;

    public function __construct(
        PresentationDao $presentationDao,
        SpecParamDao $specParamDao,
        ComponentActionDao $componentActionDao
    ) {
        $this->presentationDao = $presentationDao;
        $this->specParamDao = $specParamDao;
        $this->componentActionDao = $componentActionDao;
    }

    public function getPresentations(): array
    {
        return $this->presentationDao->findAll() ?: [];
    }

    public function getPresentation(?int $id): ?PresentationEntity
    {
        if (!$id) {
            return null;
        }
        return $this->presentationDao->find($id) ?: null;
    }

    public function savePresentation(PresentationEntity $presentation): int
    {
        return (int) $this->presentationDao->save($presentation)->getId();
    }

    public function deletePresentation(int $id): void
    {
        $this->presentationDao->delete($id);
    }

    public function getSpecParams(int $presentationId): array
    {
        return $this->specParamDao->findAllBy(['presentation_id' => $presentationId]) ?: [];
    }

    public function getComponentActions(int $presentationId): array
    {
        return $this->componentActionDao->findAllBy(['presentation_id' => $presentationId]) ?: [];
    }
}
