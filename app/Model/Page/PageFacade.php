<?php

namespace App\Model\Page;

use App\Model\PageGroup\PageGroupService;

class PageFacade
{
    private PageService $pageService;
    private PageGroupService $pageGroupService;
    private SpecParamPageService $specParamPageService;

    public function __construct(
        PageService $pageService, 
        PageGroupService $pageGroupService,
        SpecParamPageService $specParamPageService
    ) {
        $this->pageService = $pageService;
        $this->pageGroupService = $pageGroupService;
        $this->specParamPageService = $specParamPageService;
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
            
            // Načtení "Skupin stránek" (page_in_group)
            $page->page_groups = $this->pageGroupService->getPageGroupsByPageId($id);
            
            // Pro snazší kontrolu práv v šabloně přidáme i čisté pole ID skupin stránek
            $page->page_group_ids = $this->pageGroupService->getPageGroupIds($id, 'user');
            
            // Načtení "Skupin uživatelů" (page_in_group_user)
            $page->user_groups = $this->pageGroupService->getPageGroupIds($id, 'admin'); // 'admin' typ v service mapuje na page_in_group_user
            
            // Práva pro administrátory (vazba page_group -> admin_group)
            $groupIDs = array_keys((array)$page->page_groups);
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

    public function savePage(PageEntity $entity): int
    {
        return (int)$this->pageService->save($entity);
    }

    public function getPagesList(int $presentationId, ?int $excludeId = null): array
    {
        return $this->pageService->getPagesList($presentationId, $excludeId);
    }

    public function getSpecParams(int $pageId): array
    {
        return $this->specParamPageService->findByPage($pageId);
    }

    public function saveSpecParam(SpecParamPageEntity $entity): void
    {
        $this->specParamPageService->save($entity);
    }

    public function deleteSpecParam(int $id): void
    {
        $this->specParamPageService->delete($id);
    }
}
