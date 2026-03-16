<?php

namespace App\AdminModule\Presenters;

use App\Model\Object\ObjectFacade;
use App\Model\Object\ObjectEntity;
use Nette\Application\UI\Form;

final class ObjectsPresenter extends AdminPresenter
{
    /** @var ObjectFacade @inject */
    public $objectFacade;

    /** @var int|null @persistent */
    public $id;

    public function actionDefault(): void
    {
        $this->id = null;
    }

    public function renderDefault(): void
    {
        $this->template->title = 'Objekty';
        $this->template->objects = $this->objectFacade->getObjects();
    }

    public function renderEdit(?int $id = null): void
    {
        $this->template->title = $id ? 'Editace objektu' : 'Nový objekt';
        if ($id) {
            $object = $this->objectFacade->getObject($id);
            if (!$object) {
                $this->error('Objekt nebyl nalezen');
            }
            $this['objectForm']->setDefaults($object->getEntityData());
        }
    }

    public function actionDelete(int $id): void
    {
        $this->objectFacade->deleteObject($id);
        $this->flashMessage('Objekt byl smazán.');
        $this->redirect('default');
    }

    protected function createComponentObjectForm(): Form
    {
        $form = new Form;
        $form->addHidden('object_id');
        $form->addText('object_name', 'Název objektu')
            ->setRequired('Zadejte název objektu');
        $form->addText('object_code', 'Kód objektu')
            ->setRequired('Zadejte kód objektu');
        $form->addText('object_type', 'Typ objektu');
        
        $form->addSubmit('send', 'Uložit');
        $form->onSuccess[] = [$this, 'objectFormSucceeded'];
        return $form;
    }

    public function objectFormSucceeded(Form $form, \stdClass $values): void
    {
        $id = (int)$values->object_id;
        $entity = $id ? $this->objectFacade->getObject($id) : new ObjectEntity();
        $entity->fillEntity((array)$values);
        
        $this->objectFacade->saveObject($entity);
        $this->flashMessage('Objekt byl uložen.');
        $this->redirect('default');
    }
}
