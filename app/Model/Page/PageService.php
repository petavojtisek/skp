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
