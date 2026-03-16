<?php

namespace App\Model\Presentation;

class PresentationFacade
{
    private PresentationService $presentationService;

    private SpecParamDao $specParamDao;

    public function __construct(PresentationService $presentationService, SpecParamDao $specParamDao)
    {
        $this->presentationService = $presentationService;
        $this->specParamDao = $specParamDao;
    }

    public function getPresentations(): array
    {
        return $this->presentationService->getPresentations();
    }

    public function getPresentation(?int $id): ?PresentationEntity
    {
        return $this->presentationService->getPresentation($id);
    }

    public function savePresentation(PresentationEntity $presentation): int
    {
        return $this->presentationService->savePresentation($presentation);
    }

    public function deletePresentation(int $id): void
    {
        $this->presentationService->deletePresentation($id);
    }

    public function getSpecParams(int $presentationId): array
    {
        return $this->presentationService->getSpecParams($presentationId);
    }

    public function getSpecParam(int $id): ?SpecParamEntity
    {
        return $this->specParamDao->find($id);
    }

    public function saveSpecParam(SpecParamEntity $entity): SpecParamEntity
    {
        return $this->specParamDao->save($entity);
    }

    public function deleteSpecParam(int $id): void
    {
        $this->specParamDao->delete($id);
    }

    public function getComponentActions(int $presentationId): array
    {
        return $this->presentationService->getComponentActions($presentationId);
    }

    public function getAdminPresentations(int $adminId): array
    {
        return $this->presentationService->getAdminPresentations($adminId);
    }

    public function saveAdminPresentations(int $adminId, array $presentationIds): void
    {
        $this->presentationService->saveAdminPresentations($adminId, $presentationIds);
    }
}
