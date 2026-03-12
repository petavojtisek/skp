<?php

namespace App\Model\Page;

class PageFacade
{
    /** @var PageService */
    private $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function getPageTree(): array
    {
        // V reálu by PageService volal PageDao pro získání tree
        return []; 
    }
}
