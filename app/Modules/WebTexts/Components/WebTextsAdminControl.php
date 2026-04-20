<?php

namespace App\Modules\WebTexts\Components;

use App\Model\Admin\LoggedUserEntity;
use App\Model\Helper\IToolsControl;
use App\Modules\WebTexts\Model\WebTextFacade;
use App\Modules\WebTexts\Model\WebTextEntity;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class WebTextsAdminControl extends Control implements IToolsControl
{
    private WebTextFacade $webTextFacade;

    /** @var int|null @persistent */
    public $id = null;

    /** @var int @persistent */
    public $page = 1;

    /** @var string|null  */
    public $code = null;

    /** @var string|null @persistent */
    public $search = null;

    /** @var string @persistent */
    public $view = 'default';

    public LoggedUserEntity $loggedUser;


    public function __construct(WebTextFacade $webTextFacade, LoggedUserEntity $loggedUser)
    {
        $this->webTextFacade = $webTextFacade;
        $this->loggedUser = $loggedUser;
    }

    public function setCode(String $code): void
    {
        $this->code = $code;
    }

    /**
     * Unified render method
     */
    public function render(): void
    {

        $this->template->loggedUserEntity = $this->loggedUser;
        // Detail view (List or Edit)
        if ($this->view === 'edit') {
            $this->renderEdit();
            return;
        } elseif($this->view === 'list') {
            $this->renderList();
            return;
        }

       $this->template->setFile(__DIR__ . '/../templates/Admin/default.latte');
       $this->template->render();

    }

    public function renderList(): void
    {

        $limit = 20;
        $offset = ($this->page - 1) * $limit;

        $texts = $this->webTextFacade->findWebTexts($this->search, $limit, $offset);
        $totalCount = $this->webTextFacade->countWebTexts($this->search);

        $this->template->webTexts = $texts;
        $this->template->page = $this->page;
        $this->template->lastPage = ceil($totalCount / $limit);
        $this->template->search = $this->search
        ;

        $this->template->setFile(__DIR__ . '/../templates/Admin/list.latte');
        $this->template->render();
    }

    public function renderEdit(): void
    {
        if ($this->id && !$this->getComponent('webTextForm')->isSubmitted()) {
            $webText = $this->webTextFacade->getWebText($this->id);

            xdebug_break();
            if ($webText) {
                $this['webTextForm']->setDefaults($webText->getEntityData());
            }
        }

        $this->template->setFile(__DIR__ . '/../templates/Admin/edit.latte');
        $this->template->render();
    }

    /* --- SIGNALS --- */

    public function handleList(): void
    {

        $this->view = 'list';
        $this->id = null;
        $presenter = $this->getPresenter();

        $presenter->activeControl = $this->code;
        if ($presenter->isAjax()) {
            $presenter->redrawControl('tools');
            $presenter->redrawControl('webtexts');
        }

    }

    public function handleEdit(?int $id = null): void
    {

        $presenter = $this->getPresenter();
        $presenter->activeControl = $this->code;
        $this->view = 'edit';
        $this->id = $id;
        if ($presenter->isAjax()) {
            $presenter->redrawControl('tools');
            $presenter->redrawControl('webtextsEdit');
        }


    }

    public function handleDelete(int $id): void
    {

        $this->webTextFacade->deleteWebText($id);
        $presenter = $this->getPresenter();
        $presenter->activeControl = $this->code;
        if ($presenter->isAjax()) {
            $presenter->redrawControl('tools');
            $presenter->redrawControl('webtexts');
        }

        $this->getPresenter()->flashMessage('Text byl smazán.');
        $this->getPresenter()->redrawControl('flashes');

    }

    /* --- COMPONENTS --- */

    protected function createComponentSearchForm(): Form
    {

        $form = new Form;
        $form->addText('search', 'Kód')
            ->setHtmlAttribute('placeholder', 'Vyhledat podle kódu...');
        $form->addSubmit('send', 'Hledat');
        $form->setDefaults(['search' => $this->search]);
        $form->onSuccess[] = function (Form $form, $values) {

            $this->search = $values->search;
            $this->page = 1;
            if ($this->getPresenter()->isAjax()) {
                $this->redrawControl('webtexts');
            } else {
                $this->redirect('this');
            }
        };
        return $form;
    }

    protected function createComponentWebTextForm(): Form
    {
        $form = new Form;
        $form->addHidden('web_text_id');
        $form->addText('code', 'Kód')
            ->setRequired('Zadejte kód textu');
        $form->addTextArea('text', 'Text')
            ->setHtmlAttribute('class', 'wysiwyg')
            ->setHtmlAttribute('data-fm-url', $this->getPresenter()->link(':Admin:Files:default'));
        $form->addSubmit('send', 'Uložit');




        $form->onSuccess[] = [$this, 'webTextFormSucceeded'];
        return $form;
    }

    public function webTextFormSucceeded(Form $form, $values): void
    {

        if(empty($values->web_text_id)) {
            $values->web_text_id = 0;
        }

        $entity = new WebTextEntity($values);
        $this->webTextFacade->saveWebText($entity);
        $this->getPresenter()->flashMessage('Text byl uložen.');
        $this->getPresenter()->redrawControl('flashes');
        $this->handleList();
    }
}

interface IWebTextsAdminControlFactory
{
    public function create(): WebTextsAdminControl;
}
