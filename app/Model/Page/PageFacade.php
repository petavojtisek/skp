<?php

namespace App\Model\Page;

use App\Model\PageGroup\PageGroupService;

class PageFacade
{
    private PageService $pageService;
    private PageGroupService $pageGroupService;

    public function __construct(PageService $pageService, PageGroupService $pageGroupService)
    {
        $this->pageService = $pageService;
        $this->pageGroupService = $pageGroupService;
    }

    /**
     * @return PageEntity[]
     */
    public function getPages(int $presentationId): array
    {
        $pages = $this->pageService->getPagesByPresentation($presentationId);
        if (empty($pages)) {
            return [];
        }

        $tree = [];
        $references = [];

        foreach ($pages as $page) {
            $id = $page->getId();
            
            // Load Page Groups
            $groups = $this->pageGroupService->getPageGroupsByPageId($id);
            $page->page_groups = $groups;
            
            // Load Admin Groups via Page Groups
            $groupIDs = array_keys($groups);
            $page->admin_groups = $this->pageGroupService->getAdminGroupIdsByPageGroups($groupIDs);
            
            $page->children = [];
            $references[$id] = $page;
        }

        foreach ($pages as $page) {
            $parentId = $page->getPageParentId();
            if ($parentId == 0 || !isset($references[$parentId])) {
                $tree[] = $page;
            } else {
                $references[$parentId]->children[] = $page;
            }
        }

        return $tree;
    }

    public function find(int $id): ?PageEntity
    {
        return $this->pageService->find($id);
    }

    /**
     * @return array
     */
    public function getPagesList(int $presentationId, ?int $excludeId = null): array
    {
        return $this->pageService->getPagesList($presentationId, $excludeId);
    }
}
