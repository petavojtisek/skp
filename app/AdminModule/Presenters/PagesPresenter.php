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

    /** @var \App\Model\Component\ComponentFacade @inject */
    public $componentFacade;

    /** @var \App\AdminModule\Components\ObjectControlFactory @inject */
    public $objectControlFactory;

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
        $this->template->statuses = $this->lookupFacade->getLookupListOption(C_PRESENTATION_STATUS);
        $this->template->allPages = $this->pageFacade->getPagesList($presentationId, $id);

        // REAL COMPONENTS
        $this->template->pageComponents = $id ? $this->componentFacade->getByPageId($id) : [];
        $templateId = $page ? $page->getTemplateId() : 0;

        $this->template->allowedModules = $templateId ? $this->templateFacade->getAllowedModules($templateId) : [];
    }

    /**
     * AJAX signal to get dependent select options for objects modal
     */
    public function handleGetOptions(?string $type = null, ?int $moduleId = null): void
    {
        $pageId = (int)$this->id;
        $page = $this->pageFacade->find($pageId);
        $templateId = $page ? $page->getTemplateId() : 0;

        $options = [];
        if ($type === 'tab1' && $moduleId) {
            // Get all code names allowed for this module and template
            $allowed = $this->templateFacade->getAllowedCodeNames($templateId, $moduleId);

            // Get currently used code names on this page
            $used = [];
            foreach ($this->componentFacade->getByPageId($pageId) as $comp) {
                if ($comp->getModuleId() == $moduleId) {
                    $used[] = $comp->getCodeName();
                }
            }

            $diff = array_diff($allowed, $used);
            $options = array_combine($diff, $diff);
        } elseif ($type === 'tab2') {
            // Existing components that are allowed by template but NOT on this page
            $existing = $this->componentFacade->getExistingNotOnPage($pageId, $templateId);
            foreach ($existing as $c) {
                // Format: ModuleCodeName.CodeName (ComponentName)
                $label = $c->getModuleCodeName() . '.' . $c->getCodeName() . ' (' . $c->getComponentName() . ')';
                $options[$c->getId()] = $label;
            }
        }

        $this->sendJson(['options' => $options]);
    }

    public function handleAddObject(?int $pageId = null): void
    {
        $post = $this->getHttpRequest()->getPost();
        $moduleId = (int)($post['module_id'] ?? 0);
        $codeName = $post['code_name'] ?? null;
        $name = $post['name'] ?? null;

        if ($pageId && $moduleId && $codeName && $name) {
            $entity = new \App\Model\Component\ComponentEntity();
            $entity->setModuleId($moduleId);
            $entity->setCodeName($codeName);
            $entity->setComponentName($name);
            $entity->setInserted(new \DateTime());

            $compId = $this->componentFacade->save($entity);
            $this->componentFacade->linkToPage($compId, $pageId);
            $this->flashMessage('Objekt byl vytvořen a přidán na stránku.', 'success');
        }
        $this->redirect('this', ['id' => $pageId]);
    }

    public function handleLinkObject(?int $pageId = null): void
    {
        $post = $this->getHttpRequest()->getPost();
        $componentId = (int)($post['component_id'] ?? 0);

        if ($pageId && $componentId) {
            $this->componentFacade->linkToPage($componentId, $pageId);
            $this->flashMessage('Existující objekt byl přidán na stránku.', 'success');
        }
        $this->redirect('this', ['id' => $pageId]);
    }

    public function handleRemoveFromPage(int $componentId, int $pageId): void
    {
        $this->componentFacade->unlinkFromPage($componentId, $pageId);
        $this->flashMessage('Objekt byl odebrán ze stránky.');
        $this->redirect('this', ['id' => $pageId]);
    }

    public function handleDeleteFromPresentation(int $componentId, int $pageId): void
    {
        $component = $this->componentFacade->find($componentId);
        if ($component) {
            $this->componentFacade->delete($componentId);
            $this->flashMessage('Objekt byl kompletně smazán z prezentace.');
        }
        $this->redirect('this', ['id' => $pageId]);
    }

    public function handleGetEditData(int $compId): void
    {
        $component = $this->componentFacade->findWithModule($compId);
        if (!$component) $this->sendJson(['error' => 'Component not found']);

        $pageId = (int)$this->id;
        $page = $this->pageFacade->find($pageId);
        $templateId = $page ? $page->getTemplateId() : 0;

        // Get allowed code names for this module and template
        $allowed = $this->templateFacade->getAllowedCodeNames($templateId, $component->getModuleId());

        // Get used on page (excluding THIS component)
        $used = [];
        foreach ($this->componentFacade->getByPageId($pageId) as $c) {
            if ($c->getId() != $compId && $c->getModuleId() == $component->getModuleId()) {
                $used[] = $c->getCodeName();
            }
        }

        $availableCodes = array_diff($allowed, $used);
        $options = array_combine($availableCodes, $availableCodes);

        $this->sendJson([
            'id' => $component->getId(),
            'name' => $component->getComponentName(),
            'currentCode' => $component->getCodeName(),
            'options' => $options
        ]);
    }

    public function handleUpdateObject(int $compId): void
    {
        $post = $this->getHttpRequest()->getPost();
        $component = $this->componentFacade->find($compId);

        if ($component && isset($post['name']) && isset($post['code_name'])) {
            $component->setComponentName($post['name']);
            $component->setCodeName($post['code_name']);
            $this->componentFacade->save($component);
            $this->flashMessage('Nastavení objektu bylo upraveno.', 'success');
        }
        $this->redirect('this');
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

    protected function createComponentObjectControl(): \Nette\Application\UI\Multiplier
    {
        return new \Nette\Application\UI\Multiplier(function (string $compId) {
            $component = $this->componentFacade->findWithModule((int)$compId);
            if (!$component) {
                // \Tracy\Debugger::log("Component not found for ID $compId", 'error');
                return null;
            }

            $control = $this->objectControlFactory->create(
                $component->getModuleClassName(), // e.g. ContentVersionFacade
                $component->getId(),
                $component->getComponentName(),
                $component->getCodeName()
            );

            if (!$control) {
                // \Tracy\Debugger::log("Control not created for component ID $compId (Module: {$component->getModuleClassName()})", 'error');
            }

            return $control;
        });
    }
}
