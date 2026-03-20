<?php

namespace App\Model\Template;

use App\Model\Entity\CodeNameEntity;

class TemplateFacade
{
    /** @var TemplateService */
    private $templateService;

    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    public function getAllTemplates(): array
    {
        return $this->templateService->getAllTemplates();
    }

    public function getTemplate(int $id): ?TemplateEntity
    {
        return $this->templateService->getTemplate($id);
    }

    public function saveTemplate(TemplateEntity $entity): int
    {
        return $this->templateService->saveTemplate($entity);
    }

    public function deleteTemplate(int $id): void
    {
        $this->templateService->deleteTemplate($id);
    }

    public function getTemplatesByPresentation(int $presentationId): array
    {
        return $this->templateService->getTemplatesByPresentation($presentationId);
    }

    public function getTemplatesList(int $presentationId): array
    {
        return $this->templateService->getTemplatesList($presentationId);
    }

    public function getCodeNames(int $templateId): array
    {
        return $this->templateService->getCodeNames($templateId);
    }

    public function getCodeName(int $id): ?CodeNameEntity
    {
        return $this->templateService->getCodeName($id);
    }

    public function saveCodeName(CodeNameEntity $entity): int
    {
        return $this->templateService->saveCodeName($entity);
    }

    public function deleteCodeName(int $id): void
    {
        $this->templateService->deleteCodeName($id);
    }
}
