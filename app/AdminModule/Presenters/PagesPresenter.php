<?php

namespace App\AdminModule\Presenters;

use App\Model\Page\PageFacade;
use App\Model\Template\TemplateFacade;
use App\Model\Lookup\LookupFacade;

final class PagesPresenter extends AdminPresenter
{
    /** @var PageFacade @inject */
    public $pageFacade;

    /** @var TemplateFacade @inject */
    public $templateFacade;

    /** @var LookupFacade @inject */
    public $lookupFacade;

    /** @var int|null @persistent */
    public $id;

    public function renderDefault(): void
    {
        $this->template->title = 'Stránky';
        
        $presentationId = $this->loggedUserEntity->active_presentation_id;
        
        if ($presentationId) {
            $this->template->pages = $this->pageFacade->getPages($presentationId);
        } else {
            $this->template->pages = [];
        }
    }
    
    public function renderEdit(?int $id = null, ?int $parentId = null): void
    {
        $this->template->title = $id ? 'Editace stránky' : 'Nová stránka';
        $this->template->pageId = $id;
        $this->template->parentId = $parentId;
        
        $presentationId = $this->loggedUserEntity->active_presentation_id;

        // Load page if exists
        $page = null;
        if ($id) {
            // We need a find method in PageFacade
            $page = $this->pageFacade->find($id);
        }
        $this->template->page = $page;

        // Templates for current presentation
        $this->template->templates = $this->templateFacade->getTemplatesList($presentationId);

        // Statuses from lookup
        $this->template->statuses = $this->lookupFacade->getLookupList(C_PRESENTATION_STATUS);

        // Redirect autocomplete - other pages in same presentation
        $this->template->allPages = $this->pageFacade->getPagesList($presentationId, $id);

        $this->template->pageObjects = [
            ['id' => 1, 'type' => 'Obsah', 'code' => 'content.about_us', 'title' => 'Hlavní text', 'content' => '<p>Jsme sdružení...</p>'],
            ['id' => 2, 'type' => 'Galerie', 'code' => 'gallery.about', 'title' => 'Fotky z akcí', 'content' => '[Galerie 5 obrázků]'],
        ];
    }

    public function handleMove(?int $id = null, ?int $parentId = null, ?int $position = null): void
    {
        if ($id) {
            $this->flashMessage("Stránka byla přesunuta (ID: $id, Parent: $parentId, Pos: $position).", 'success');
        }

        if ($this->isAjax()) {
            $this->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }

    /**
     * Helper to generate slug from string (AJAX or manual)
     */
    public function handleGenerateRewrite(?string $name = null): void
    {
        if (!$name) {
            $this->sendJson(['rewrite' => '']);
            return;
        }
        $rewrite = \Nette\Utils\Strings::webalize($name);
        $this->sendJson(['rewrite' => $rewrite]);
    }
}
