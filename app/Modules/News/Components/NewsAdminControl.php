<?php

namespace App\Modules\News\Components;

use App\Model\Admin\LoggedUserEntity;
use App\Model\Element\ElementEntity;
use App\Model\Element\ElementFacade;
use App\Model\Helper\ImageResizer;
use App\Model\Helper\IObjectControl;
use App\Model\Lookup\LookupFacade;
use App\Modules\News\Model\NewsEntity;
use App\Modules\News\Model\NewsFacade;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\User;

class NewsAdminControl extends Control implements IObjectControl
{

    public static $imagePath = 'news';

    /** @persistent */
    public string $view = 'list';

    /** @persistent */
    public ?int $elementId = null;

    private int $componentId;
    private string $name;
    private string $code;

    private NewsFacade $facade;
    private ElementFacade $elementFacade;
    private LookupFacade $lookupFacade;
    private ImageResizer $imageResizer;
    private User $user;

    private LoggedUserEntity $loggedUserEntity;

    public function __construct(
        NewsFacade $facade,
        ElementFacade $elementFacade,
        LookupFacade $lookupFacade,
        ImageResizer $imageResizer,
        User $user,
        LoggedUserEntity $loggedUser
    ) {
        $this->facade = $facade;
        $this->elementFacade = $elementFacade;
        $this->lookupFacade = $lookupFacade;
        $this->imageResizer = $imageResizer;
        $this->user = $user;
        $this->loggedUserEntity = $loggedUser;
    }

    public function setComponentId(int $componentId): void
    {
        $this->componentId = $componentId;
    }

    public function setComponentInfo(string $name, string $code): void
    {
        $this->name = $name;
        $this->code = $code;
    }

    public function render(): void
    {

        $this->template->loggedUserEntity = $this->loggedUserEntity;

        $this->template->name = $this->name;
        $this->template->code = $this->code;
        $this->template->componentId = $this->componentId;
        $this->template->view = $this->view;
        $this->template->states = $this->lookupFacade->getLookupListOption(C_ELEMENT_STATUS);

        if ($this->view === 'edit') {
            $item = $this->elementId ? $this->elementFacade->find($this->elementId) : new ElementEntity();
            $this->template->item = $item;
            $this->template->setFile(__DIR__ . '/../templates/Admin/edit.latte');
        } else {
            $this->template->items = $this->facade->getByComponentId($this->componentId);
            $this->template->setFile(__DIR__ . '/../templates/Admin/list.latte');
        }

        $this->template->render();
    }


    public function handleDelete(int $elementId): void
    {
        $news = $this->facade->find($elementId);
        if ($news && $news->getImage()) {
            $this->imageResizer->deleteNewsImage($news->getImage(),self::$imagePath);
        }
        $this->facade->delete($elementId);
        $this->elementFacade->delete($elementId);
        $this->getPresenter()->flashMessage("Aktualita byla smazána.", 'success');
        $this->redrawControl();
    }

    public function handleEdit(?int $elementId = null): void
    {
        $this->view = 'edit';
        $this->elementId = $elementId;
        $this->template->newsEntity = $this->facade->find($this->elementId);
        $this->redrawControl();
    }


    public function handleBack(): void
    {
        $this->view = 'list';
        $this->elementId = null;
        $this->redrawControl();
    }

    public function handleRemoveFromPage(): void
    {
        $this->getPresenter()->handleRemoveFromPage($this->componentId, (int)$this->getPresenter()->id);
    }

    public function handleDeleteFromPresentation(): void
    {
        $items = $this->facade->getByComponentId($this->componentId);
        foreach ($items as $item) {
            if ($item->getImage()) {
                $this->imageResizer->deleteNewsImage($item->getImage(), self::$imagePath);
            }
            $this->facade->delete($item->getId());
            $this->elementFacade->delete($item->getId());
        }
        $this->getPresenter()->handleDeleteFromPresentation($this->componentId, (int)$this->getPresenter()->id);
    }

    // --- Edit Form ---

    protected function createComponentEditForm(): Form
    {
        $form = new Form();

        $form->addHidden('element_id');
        $form->addText('name', 'Interní název')
            ->setRequired('Zadejte interní název');

        $form->addText('title', 'Titulek')
            ->setRequired('Zadejte titulek');

        $statuses = $this->lookupFacade->getLookupListOption(C_ELEMENT_STATUS);
        $form->addSelect('status_id', 'Stav', $statuses)
            ->setRequired('Vyberte stav');

        $form->addText('valid_from', 'Platnost od')
            ->setHtmlType('date');
        $form->addText('valid_to', 'Platnost do')
            ->setHtmlType('date');

        $form->addTextArea('short_text', 'Krátký text')
            ->setHtmlAttribute('rows', 3);

        $form->addTextArea('content', 'Obsah')
            ->setHtmlAttribute('class', 'wysiwyg')
            ->setHtmlAttribute('data-fm-url', $this->getPresenter()->link(':Admin:Files:default'));

        $form->addUpload('image', 'Obrázek')
            ->addRule(Form::IMAGE, 'Soubor musí být obrázek.');

        $form->addSubmit('save', 'Uložit aktualitu');

        if ($this->elementId) {
            $element = $this->elementFacade->find($this->elementId);
            $news = $this->facade->find($this->elementId);
            if ($element && $news) {
                $values = $element->getEntityData();
                $values['title'] = $news->getTitle();
                $values['short_text'] = $news->getShortText();
                $values['content'] = $news->getContent();

                if ($element->getValidFrom()) $values['valid_from'] = $element->getValidFrom('Y-m-d');
                if ($element->getValidTo()) $values['valid_to'] = $element->getValidTo('Y-m-d');

                $form->setDefaults($values);
            }
        }

        $form->onSuccess[] = [$this, 'editFormSucceeded'];
        return $form;
    }

    public function editFormSucceeded(Form $form, array $values): void
    {
        $id = $values['element_id'] ? (int)$values['element_id'] : null;

        $element = $id ? $this->elementFacade->find($id) : new ElementEntity();
        $element->setComponentId($this->componentId);
        $element->setName($values['name']);
        $element->setStatusId($values['status_id']);
        $element->setValidFrom($values['valid_from'] ?: null);
        $element->setValidTo($values['valid_to'] ?: null);

        $elementId = $this->elementFacade->save($element, $this->user->getId());

        $news = $id ? $this->facade->find($id) : new NewsEntity();
        $news->setId($elementId);
        $news->setTitle($values['title']);
        $news->setShortText($values['short_text']);
        $news->setContent($values['content']);

        /** @var \Nette\Http\FileUpload $image */
        $image = $values['image'];
        if ($image->isOk()) {
            if ($news->getImage()) {
                $this->imageResizer->deleteNewsImage($news->getImage(), self::$imagePath);
            }
            $filename = $this->imageResizer->processNewsImage($image, self::$imagePath);
            $news->setImage($filename);
        }

        $this->facade->save($news);

        $this->getPresenter()->flashMessage('Aktualita byla uložena.', 'success');

        $this->view = 'list';
        $this->elementId = null;

        if ($this->getPresenter()->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }
}

interface INewsAdminControlFactory
{
    public function create(): NewsAdminControl;
}
