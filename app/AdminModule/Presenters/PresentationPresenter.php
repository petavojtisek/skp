<?php

namespace App\AdminModule\Presenters;

use App\Model\Presentation\PresentationFacade;
use App\Model\Presentation\PresentationEntity;
use App\Model\Presentation\SpecParamEntity;
use Nette\Application\UI\Form;

final class PresentationPresenter extends AdminPresenter
{
    /** @var PresentationFacade @inject */
    public $presentationFacade;

    /** @var int|null @persistent */
    public $id;

    /** @var int|null @persistent */
    public $spec_param_id;

    public function actionDefault(): void
    {
        $this->id = null;
        $this->spec_param_id = null;
    }

    public function renderDefault(): void
    {
        $this->template->title = 'Prezentace';
        $this->template->presentations = $this->presentationFacade->getPresentations();
    }

    public function renderEdit(?int $id = null): void
    {
        if ($id === null and $this->id !== null) {
            $id = (int)$this->id;
        }

        $this->template->title = $id ? 'Editace prezentace' : 'Nová prezentace';
        $this->template->presentationId = $id;
        $this->template->spec_param_id = $this->spec_param_id;

        if ($id) {
            $presentation = $this->presentationFacade->getPresentation($id);
            if (!$presentation) {
                if ($this->isAjax()) {
                    return;
                }
                $this->error('Prezentace nebyla nalezena');
            }
            $this['basicForm']->setDefaults($presentation->getEntityData());

            // SpecParams & ComponentActions
            $this->template->specParams = $this->presentationFacade->getSpecParams($id);
            $this->template->componentActions = $this->presentationFacade->getComponentActions($id);

            // If we are editing a spec param
            if ($this->spec_param_id) {
                $param = $this->presentationFacade->getSpecParam((int)$this->spec_param_id);
                if ($param) {
                    $this['specParamForm']->setDefaults($param->getEntityData());
                }
            }
        } else {
            $this->template->specParams = [];
            $this->template->componentActions = [];
        }
    }

    public function actionDelete(?int $id = null): void
    {
        if ($id) {
            $this->presentationFacade->deletePresentation($id);
            $this->flashMessage('Prezentace byla smazána.');
        }
        $this->redirect('default');
    }

    /**
     * Signal for AJAX editing of spec param
     */
    public function handleEditSpecParam(int $specParamId): void
    {
        $this->spec_param_id = $specParamId;
        if ($this->isAjax()) {
            $this->redrawControl('specParamFormSnippet');
        } else {
            $this->redirect('this');
        }
    }

    /**
     * Signal for AJAX canceling of spec param edit
     */
    public function handleCancelSpecParam(): void
    {
        $this->spec_param_id = null;
        if ($this->isAjax()) {
            $this['specParamForm']->setValues([], true);
            $this->redrawControl('specParamFormSnippet');
        } else {
            $this->redirect('this');
        }
    }

    /**
     * Signal for deleting spec param (AJAX supported)
     */
    public function handleDeleteSpecParam(int $specParamId, int $id): void
    {
        $this->presentationFacade->deleteSpecParam($specParamId);
        $this->flashMessage('Parametr byl smazán.');
        $this->id = $id;

        if ($this->isAjax()) {
            $this->template->presentationId = $id;
            $this->template->specParams = $this->presentationFacade->getSpecParams($id);
            $this->redrawControl('specParamsTableSnippet');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('edit#tab-spec-params', ['id' => $id]);
        }
    }

    protected function createComponentBasicForm(): Form
    {
        $form = new Form;

        $form->addHidden('presentation_id');

        $form->addText('presentation_name', 'Název prezentace')
            ->setRequired('Zadejte název prezentace');

        $form->addText('domain', 'Doména')
            ->setRequired('Zadejte doménu');

        $form->addText('directory', 'Adresář')
            ->setRequired('Zadejte adresář');

        $form->addTextArea('presentation_description', 'Popis');
        $form->addTextArea('presentation_keywords', 'Klíčová slova');

        $form->addCheckbox('is_default', 'Výchozí prezentace');

        $form->addSubmit('send', 'Uložit základní nastavení')
            ->setHtmlAttribute('class', 'btn btn-success');

        $form->onSuccess[] = $this->basicFormSucceeded(...);
        return $form;
    }

    public function basicFormSucceeded(Form $form, \stdClass $values): void
    {
        $id = (int) $values->presentation_id;
        unset($values->presentation_id);

        $presentation = $id ? $this->presentationFacade->getPresentation($id) : new PresentationEntity();
        $presentation->fillEntity((array) $values);

        if (!$id) {
            $presentation->presentation_lang = C_LANGUAGE_CS;
            $presentation->presentation_status = C_PRESENTATION_STATUS_ACTIVE;
        }

        $newId = $this->presentationFacade->savePresentation($presentation);
        $this->flashMessage('Základní nastavení bylo uloženo.');

        if ($this->isAjax()) {
            $this->redrawControl('flashes');
            $this->redrawControl('basicFormSnippet');
        } else {
            if (!$id) {
                $this->redirect('edit', ['id' => $newId]);
            }
            $this->redirect('this');
        }
    }

    protected function createComponentSpecParamForm(): Form
    {
        $form = new Form;
        $form->addHidden('spec_param_id');
        $form->addHidden('presentation_id');

        $form->addText('name', 'Název')
            ->setRequired('Zadejte název parametru');

        $form->addText('value', 'Hodnota')
            ->setRequired('Zadejte hodnotu parametru');

        $form->addSubmit('send', 'Uložit parametr')
            ->setHtmlAttribute('class', 'btn btn-primary');

        $form->onSuccess[] = $this->specParamFormSucceeded(...);
        return $form;
    }

    public function specParamFormSucceeded(Form $form, \stdClass $values): void
    {
        $presentationId = (int) $values->presentation_id;

        $entity = new SpecParamEntity();
        $entity->fillEntity((array) $values);

        $entity = $this->presentationFacade->saveSpecParam($entity);
        $this->flashMessage('Parametr byl uložen.');

        if ($this->isAjax()) {
            $this->spec_param_id = null;
            $form->setValues([], true);
            $this->template->specParams = $this->presentationFacade->getSpecParams($presentationId);
            $this->redrawControl('specParamFormSnippet');
            $this->redrawControl('specParamsTableSnippet');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('edit#tab-spec-params', ['id' => $presentationId, 'spec_param_id' => null]);
        }
    }
}
