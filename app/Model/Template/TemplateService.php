<?php

namespace App\Model\Template;

use App\Model\Base\BaseService;
use App\Model\Entity\CodeNameEntity;

class TemplateService extends BaseService
{
    /** @var TemplateDao */
    private $templateDao;

    /** @var CodeNameDao */
    private $codeNameDao;

    public function __construct(TemplateDao $templateDao, CodeNameDao $codeNameDao)
    {
        $this->templateDao = $templateDao;
        $this->codeNameDao = $codeNameDao;
    }

    public function getAllTemplates(): array
    {
        return $this->templateDao->findAll() ?: [];
    }

    public function getTemplate(int $id): ?TemplateEntity
    {
        return $this->templateDao->find($id) ?: null;
    }

    public function saveTemplate(TemplateEntity $entity): int
    {
        return $entity->getId() ? $this->templateDao->update($entity) : (int) $this->templateDao->insert($entity);
    }

    public function deleteTemplate(int $id): void
    {
        $this->templateDao->delete($id);
    }

    public function getTemplatesByPresentation(int $presentationId): array
    {
        return $this->templateDao->findAllBy(['presentation_id' => $presentationId]) ?: [];
    }

    public function getTemplatesList(int $presentationId): array
    {
        $templates = $this->getTemplatesByPresentation($presentationId);
        $list = [];
        foreach ($templates as $t) {
            $list[$t->getId()] = $t->getTemplateName() . ' (' . $t->getTemplateFilename() . ')';
        }
        return $list;
    }

    public function getCodeNames(int $templateId): array
    {
        return $this->codeNameDao->findAllBy(['template_id' => $templateId]) ?: [];
    }

    public function getCodeName(int $id): ?CodeNameEntity
    {
        return $this->codeNameDao->find($id) ?: null;
    }

    public function saveCodeName(CodeNameEntity $entity): int
    {
        return $entity->getId() ? $this->codeNameDao->update($entity) : (int) $this->codeNameDao->insert($entity);
    }

    public function deleteCodeName(int $id): void
    {
        $this->codeNameDao->delete($id);
    }
}
