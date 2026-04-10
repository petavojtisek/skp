<?php

namespace App\Model\Page;

use App\Model\Base\BaseService;

class PageService extends BaseService
{
    /** @var PageDao */
    private $pageDao;

    public function __construct(PageDao $pageDao)
    {
        $this->pageDao = $pageDao;
    }

    /**
     * @return PageEntity[]
     */
    public function getPagesByPresentation(int $presentationId): array
    {
        $pages = $this->pageDao->findAllBy(['presentation_id' => $presentationId], null, null, 'position ASC');
        return $pages ?: [];
    }

    public function find(int $id): ?PageEntity
    {
        return $this->pageDao->find($id) ?: null;
    }

    public function save(PageEntity $entity): int
    {
        return (int)$this->pageDao->save($entity)->getId();
    }

    public function getByRewrite(string $rewrite, int $presentationId): ?PageEntity
    {
        return $this->pageDao->findByRewrite($rewrite, $presentationId);
    }

    public function getPageById(int $pageId, int $presentationId): ?PageEntity
    {
        return $this->pageDao->getPageById($pageId, $presentationId);
    }

    public function getDefaultPage(int $presentationId, int $activeStatus): ?PageEntity
    {
        return $this->pageDao->getDefaultPage($presentationId, $activeStatus);
    }

    public function getComponentActions(int $pageId): array
    {
        return $this->pageDao->getComponentActions($pageId);
    }

    /**
     * @return array id => name
     */
    public function getPagesList(int $presentationId, ?int $excludeId = null): array
    {
        $where = ['presentation_id' => $presentationId];
        if ($excludeId) {
            $where[] = ['page_id != %i', $excludeId];
        }

        $pages = $this->pageDao->findAllBy($where, null, null, 'page_name ASC');
        $list = [];
        if ($pages) {
            foreach ($pages as $p) {
                $list[$p->getId()] = $p->getPageName();
            }
        }
        return $list;
    }
}
