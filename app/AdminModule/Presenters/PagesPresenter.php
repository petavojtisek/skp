<?php

namespace App\AdminModule\Presenters;

use App\Model\Page\PageFacade;
use App\Model\Template\TemplateFacade;
use App\Model\Lookup\LookupFacade;
use App\Model\Page\SpecParamPageEntity;
use App\Model\Page\PageEntity;
use App\Model\AdminGroup\AdminGroupFacade;
use App\Model\PageGroup\PageGroupFacade;

final class PagesPresenter extends AdminPresenter
{
    /** @var PageFacade @inject */
    public $pageFacade;

    /** @var TemplateFacade @inject */
    public $templateFacade;

    /** @var LookupFacade @inject */
    public $lookupFacade;

    /** @var AdminGroupFacade @inject */
    public $adminGroupFacade;

    /** @var PageGroupFacade @inject */
    public $pageGroupFacade;

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
        $id = $id ?: $this->id;
        
        $this->template->title = $id ? 'Editace stránky' : 'Nová stránka';
        $this->template->pageId = $id;
        $this->template->parentId = $parentId;
        
        $presentationId = $this->loggedUserEntity->active_presentation_id;

        $page = null;
        if ($id) {
            $page = $this->pageFacade->find($id);
            $this->template->specParams = $this->pageFacade->getSpecParams($id);
            
            // Groups for Page Groups tab
            $this->template->allPageGroups = $this->pageGroupFacade->getPageGroups();
            $this->template->activeUserGroupIds = $this->pageGroupFacade->getPageInGroupIds($id);
            $this->template->activeAdminGroupIds = $this->pageGroupFacade->getPageInGroupUserIds($id);

        } else {
            $this->template->specParams = [];
            $this->template->allPageGroups = [];
            $this->template->activeUserGroupIds = [];
            $this->template->activeAdminGroupIds = [];
        }
        $this->template->page = $page;

        $this->template->templates = $this->templateFacade->getTemplatesList($presentationId);
        $this->template->statuses = $this->lookupFacade->getLookupList(C_PRESENTATION_STATUS);
        $this->template->allPages = $this->pageFacade->getPagesList($presentationId, $id);

        $this->template->pageObjects = [
            ['id' => 1, 'type' => 'Obsah', 'code' => 'content.about_us', 'title' => 'Hlavní text', 'content' => '<p>Jsme sdružení...</p>'],
            ['id' => 2, 'type' => 'Galerie', 'code' => 'gallery.about', 'title' => 'Fotky z akcí', 'content' => '[Galerie 5 obrázků]'],
        ];

        // Dummy data for Add Object modal
        $this->template->tab1Modules = [
            'content' => 'Obsahové moduly',
            'gallery' => 'Galerie a obrázky',
            'news' => 'Aktuality a novinky'
        ];

        $this->template->tab2Modules = [
            'form' => 'Kontaktní formuláře',
            'map' => 'Mapové podklady',
            'custom' => 'Vlastní skripty'
        ];
    }

    /**
     * AJAX signal to get dependent select options for objects modal
     */
    public function handleGetOptions(?string $type = null, ?string $moduleId = null): void
    {
        $options = [];
        if ($type === 'tab1') {
            $data = [
                'content' => ['content.text' => 'Prostý text', 'content.html' => 'HTML blok'],
                'gallery' => ['gallery.slider' => 'Slider fotek', 'gallery.grid' => 'Mřížka'],
                'news' => ['news.list' => 'Seznam novinek', 'news.detail' => 'Detail článku']
            ];
            $options = $data[$moduleId] ?? [];
        } elseif ($type === 'tab2') {
            $data = [
                'form' => ['form.contact' => 'Kontaktní formulář', 'form.order' => 'Objednávka'],
                'map' => ['map.google' => 'Google Maps', 'map.seznam' => 'Mapy.cz'],
                'custom' => ['custom.code' => 'Vlastní kód', 'custom.js' => 'JavaScript blok']
            ];
            $options = $data[$moduleId] ?? [];
        }

        $this->sendJson(['options' => $options]);
    }

    public function handleSave(?int $id = null, ?int $parentId = null): void
    {
        $post = $this->getHttpRequest()->getPost();
        
        $entity = $id ? $this->pageFacade->find($id) : new PageEntity();
        if (!$entity) {
            $this->flashMessage('Stránka nebyla nalezena.', 'danger');
            $this->redirect('default');
        }

        $entity->setPageName($post['page_name'] ?? null);
        $entity->setPageRewrite($post['page_rewrite'] ?? null);
        $entity->setPageTitle($post['page_title'] ?? null);
        $entity->setPageDescription($post['page_description'] ?? null);
        $entity->setPageKeywords($post['page_keywords'] ?? null);
        $entity->setPageRedirect($post['page_redirect'] ?? null);
        $entity->setPosition((int)($post['position'] ?? 0));
        $entity->setPageStatus((int)($post['page_status'] ?? 0));
        $entity->setTemplateId($post['template_id'] ? (int)$post['template_id'] : null);
        
        $entity->setPageSitemap(isset($post['page_sitemap']) ? 'Y' : 'N');
        $entity->setPageMenu(isset($post['page_menu']) ? 'Y' : 'N');
        $entity->setRestrictedArea(isset($post['restricted_area']) ? 'Y' : 'N');

        if (!$id) {
            $entity->setPageParentId($parentId !== null ? (int)$parentId : 0);
            $entity->setPresentationId($this->loggedUserEntity->active_presentation_id);
        }

        $newId = $this->pageFacade->savePage($entity);

        // Automatické přiřazení skupiny 1 pro novou stránku
        if (!$id) {
            $this->pageGroupFacade->togglePageInGroup($newId, 1, true);
            $this->pageGroupFacade->togglePageInGroupUser($newId, 1, true);
        }
        
        $this->flashMessage('Stránka byla úspěšně uložena.', 'success');
        $this->redirect('edit', ['id' => $newId]);
    }

    public function handleMove(?int $id = null, ?int $parentId = null, ?int $position = null): void
    {
        if ($id) {
            $page = $this->pageFacade->find($id);
            if ($page) {
                // Kontrola práv pro přesun (stejná logika jako v šabloně)
                $userPageGroupIds = array_keys($this->loggedUserEntity->rights['page_rights'] ?? []);
                $isMember = !empty(array_intersect($userPageGroupIds, (array)$page->page_group_ids));
                
                if ($this->loggedUserEntity->hasGroupRight('EDIT_PAGE') or $isMember) {
                    $this->pageFacade->movePage($id, (int)$parentId, (int)$position);
                    $this->flashMessage("Stránka '{$page->getPageName()}' byla přesunuta.", 'success');
                } else {
                    $this->flashMessage("Nemáte oprávnění k přesunu této stránky.", 'danger');
                }
            }
        }

        if ($this->isAjax()) {
            $this->redrawControl('flashes');
            $this->redrawControl('pageTree');
        } else {
            $this->redirect('this');
        }
    }

    public function handleGenerateRewrite(?string $name = null): void
    {
        if (!$name) {
            $this->sendJson(['rewrite' => '']);
            return;
        }
        $rewrite = \Nette\Utils\Strings::webalize($name);
        $this->sendJson(['rewrite' => $rewrite]);
    }

    /**
     * AJAX signal to add special parameter
     */
    public function handleAddParam(?int $pageId = null, ?string $name = null, ?string $value = null): void
    {
        if (!$pageId or !$name or !$value) {
            if (!$this->isAjax()) $this->flashMessage('Název i hodnota parametru musí být vyplněny.', 'danger');
        } else {
            $entity = new SpecParamPageEntity();
            $entity->setPageId($pageId);
            $entity->setName($name);
            $entity->setValue($value);
            
            $this->pageFacade->saveSpecParam($entity);
            $this->flashMessage('Parametr byl přidán.', 'success');
        }

        if ($this->isAjax()) {
            $this->redrawControl('flashes');
            $this->redrawControl('specParams');
        } else {
            $this->redirect('this', ['id' => $pageId]);
        }
    }

    /**
     * AJAX signal to delete special parameter
     */
    public function handleDeleteParam(?int $paramId = null, ?int $pageId = null): void
    {
        if ($paramId) {
            $this->pageFacade->deleteSpecParam($paramId);
            $this->flashMessage('Parametr byl smazán.', 'success');
        }

        if ($this->isAjax()) {
            $this->redrawControl('flashes');
            $this->redrawControl('specParams');
        } else {
            $this->redirect('this', ['id' => $pageId]);
        }
    }

    public function handleToggleGroup(?int $pageId = null, ?int $groupId = null, $state = null, ?string $type = 'user'): void
    {
        if ($pageId and $groupId and $state !== null) {
            if ($type === 'admin') {
                $this->pageGroupFacade->togglePageInGroupUser($pageId, $groupId, (bool)$state);
            } else {
                $this->pageGroupFacade->togglePageInGroup($pageId, $groupId, (bool)$state);
            }
            $this->flashMessage('Nastavení skupiny bylo změněno.', 'success');
        }
        
        if ($this->isAjax()) {
            $this->redrawControl('flashes');
            $this->redrawControl('pageGroups');
        } else {
            $this->redirect('this', ['id' => $pageId]);
        }
    }
}
