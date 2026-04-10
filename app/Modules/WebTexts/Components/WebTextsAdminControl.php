<?php

namespace App\Modules\WebTexts\Components;

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

    /** @var string|null @persistent */
    public $code = null;

    /** @var string @persistent */
    public $view = 'default';

    public function __construct(WebTextFacade $webTextFacade)
    {
        $this->webTextFacade = $webTextFacade;
    }

    /**
     * Unified render method
     */
    public function render(): void
    {
        $presenter = $this->getPresenter();

        // Dashboard view (Small Box)

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

        $texts = $this->webTextFacade->findWebTexts($this->code, $limit, $offset);
        $totalCount = $this->webTextFacade->countWebTexts($this->code);

        $this->template->webTexts = $texts;
        $this->template->page = $this->page;
        $this->template->lastPage = ceil($totalCount / $limit);
        $this->template->code = $this->code;

        $this->template->setFile(__DIR__ . '/../templates/Admin/list.latte');
        $this->template->render();
    }

    public function renderEdit(): void
    {
        if ($this->id && !$this->getComponent('webTextForm')->isSubmitted()) {
            $webText = $this->webTextFacade->getWebText($this->id);
            if ($webText) {
                $this['webTextForm']->setDefaults($webText->toArray());
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
        $this->redirect('this');
    }

    public function handleEdit(?int $id = null): void
    {
        $this->view = 'edit';
        $this->id = $id;
        $this->redirect('this');
    }

    public function handleDelete(int $id): void
    {
        $this->webTextFacade->deleteWebText($id);
        $this->getPresenter()->flashMessage('Text byl smazán.');
        $this->redirect('this', ['view' => 'list', 'id' => null]);
    }

    /* --- COMPONENTS --- */

    protected function createComponentSearchForm(): Form
    {
        $form = new Form;
        $form->addText('code', 'Kód')
            ->setHtmlAttribute('placeholder', 'Vyhledat podle kódu...');
        $form->addSubmit('send', 'Hledat');
        $form->setDefaults(['code' => $this->code]);
        $form->onSuccess[] = function (Form $form, $values) {
            $this->code = $values->code;
            $this->page = 1;
            $this->redirect('this');
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
            ->setHtmlAttribute('class', 'editor');
        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = [$this, 'webTextFormSucceeded'];
        return $form;
    }

    public function webTextFormSucceeded(Form $form, $values): void
    {
        $entity = new WebTextEntity($values);
        $this->webTextFacade->saveWebText($entity);
        $this->getPresenter()->flashMessage('Text byl uložen.');
        $this->redirect('this', ['view' => 'list', 'id' => null]);
    }
}

interface IWebTextsAdminControlFactory
{
    public function create(): WebTextsAdminControl;
}
