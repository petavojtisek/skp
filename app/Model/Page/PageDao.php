<?php

namespace App\Model\Page;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class PageDao extends BaseDao
{
    protected string $entityName = 'Page\\PageEntity';

    /** @var PageMapper */
    protected IMapper $mapper;

    public function __construct(PageMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function findByRewrite(string $rewrite, int $presentationId): ?PageEntity
    {
        $data = $this->mapper->getByRewrite($rewrite, $presentationId);
        return $data ? $this->getEntity($this->entityName, $data) : null;
    }

    public function getPageById(int $pageId, int $presentationId): ?PageEntity
    {
        $data = $this->mapper->findOneBy(['page_id' => $pageId, 'presentation_id' => $presentationId]);
        return $data ? $this->getEntity($this->entityName, (array)$data) : null;
    }

    public function getDefaultPage(int $presentationId, int $activeStatus): ?PageEntity
    {
        // Typically default page is the one with parent_id = 0 and lowest position, or has a specific flag.
        // Based on the router logic, it seems we just need 'a' default page.
        // Let's assume it's the one with parent_id = 0 and status = active.
        $data = $this->mapper->findOneBy([
            'presentation_id' => $presentationId,
            'page_status' => $activeStatus,
            'page_parent_id' => 0
        ], 'position ASC');

        return $data ? $this->getEntity($this->entityName, (array)$data) : null;
    }

    public function getComponentActions(int $pageId): array
    {
        return $this->mapper->getComponentActions($pageId);
    }
}
