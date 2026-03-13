<?php

namespace App\AdminModule\Presenters;

use App\Model\Entity\CodeNameEntity;
use App\Model\Template\TemplateFacade;
use App\Model\Template\TemplateEntity;
use App\Model\Presentation\PresentationFacade;
use Nette\Application\UI\Form;

final class TemplatePresenter extends AdminPresenter
{
    /** @var TemplateFacade @inject */
    public $templateFacade;

    /** @var PresentationFacade @inject */
    public $presentationFacade;

    /** @var int|null @persistent */
    public $id;

    /** @var int|null @persistent */
    public $code_name_id;

    public function renderDefault(): void
    {
        $this->template->title = 'Šablony';
        $this->template->templates = $this->templateFacade->getAllTemplates();
    }

    public function renderEdit(?int $id = null): void
    {
        if ($id === null && $this->id !== null) {
            $id = (int)$this->id;
        }

        $this->template->title = $id ? 'Editace šablony' : 'Nová šablona';
        $this->template->templateId = $id;
        $this->template->code_name_id = $this->code_name_id;

        if ($id) {
            $template = $this->templateFacade->getTemplate($id);
            if (!$template) {
                if ($this->isAjax()) {
                    return;
                }
                $this->error('Šablona nebyla nalezena');
            }
            $this['templateForm']->setDefaults($template->getEntityData());

            // CodeNames
            $this->template->codeNames = $this->templateFacade->getCodeNames($id);

            // If editing a code name
            if ($this->code_name_id) {
                $cn = $this->templateFacade->getCodeName((int)$this->code_name_id);
                if ($cn) {
                    $this['codeNameForm']->setDefaults($cn->getEntityData());
                }
            }
        } else {
            $this->template->codeNames = [];
        }
    }

    public function actionDelete(int $id): void
    {
        $this->templateFacade->deleteTemplate($id);
        $this->flashMessage('Šablona byla smazána.');
        $this->redirect('default');
    }

    /**
     * Signal for AJAX editing of code name
     */
    public function handleEditCodeName(int $codeNameId): void
    {
        $this->code_name_id = $codeNameId;
        if ($this->isAjax()) {
            $this->redrawControl('codeNameFormSnippet');
        } else {
            $this->redirect('this');
        }
    }

    /**
     * Signal for AJAX canceling of edit
     */
    public function handleCancelEdit(): void
    {
        $this->code_name_id = null;
        if ($this->isAjax()) {
            $this['codeNameForm']->setValues([], true); // Reset form
            $this->redrawControl('codeNameFormSnippet');
        } else {
            $this->redirect('this');
        }
    }

    /**
     * Signal for deleting code name (AJAX supported)
     */
    public function handleDeleteCodeName(int $codeNameId, int $id): void
    {
        $this->templateFacade->deleteCodeName($codeNameId);
        $this->flashMessage('Kódové označení bylo smazáno.');
        $this->id = $id;

        if ($this->isAjax()) {
            $this->template->codeNames = $this->templateFacade->getCodeNames($id);
            $this->redrawControl('codeNamesTableSnippet');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('edit#tab-code-names', ['id' => $id]);
        }
    }

    protected function createComponentTemplateForm(): Form
    {
        $form = new Form;
        $form->addHidden('template_id');

        $form->addText('template_name', 'Název šablony')
            ->setRequired('Zadejte název šablony');

        $form->addText('template_filename', 'Název souboru (filename.tpl)')
            ->setRequired('Zadejte název souboru');

        $form->addText('template_path', 'Cesta k šabloně')
            ->setRequired('Zadejte cestu k šabloně');

        $presentations = [];
        foreach ($this->presentationFacade->getPresentations() as $p) {
            $presentations[$p->presentation_id] = $p->presentation_name;
        }
        $form->addSelect('presentation_id', 'Prezentace', $presentations)
            ->setPrompt('Zvolte prezentaci')
            ->setRequired('Zvolte prezentaci');

        $form->addSubmit('send', 'Uložit šablonu')
            ->setHtmlAttribute('class', 'btn btn-success');

        $form->onSuccess[] = $this->templateFormSucceeded(...);
        return $form;
    }

    public function templateFormSucceeded(Form $form, \stdClass $values): void
    {
        $entity = new TemplateEntity();
        $entity->fillEntity((array) $values);

        $newId = $this->templateFacade->saveTemplate($entity);
        $this->flashMessage('Šablona byla uložena.');

        if (!$values->template_id) {
            $this->redirect('edit', ['id' => $newId]);
        }
        $this->redirect('this');
    }

    protected function createComponentCodeNameForm(): Form
    {
        $form = new Form;
        $form->addHidden('id');
        $form->addHidden('template_id');

        $form->addText('code_name', 'Kódové označení')
            ->setRequired('Zadejte kódové označení');

        $form->addInteger('module', 'Modul (ID)')
            ->setDefaultValue(0);

        $form->addSubmit('send', 'Uložit kód')
            ->setHtmlAttribute('class', 'btn btn-primary');

        $form->onSuccess[] = $this->codeNameFormSucceeded(...);
        return $form;
    }

    public function codeNameFormSucceeded(Form $form, \stdClass $values): void
    {
        $templateId = (int) $values->template_id;

        $entity = new CodeNameEntity();
        $entity->fillEntity((array) $values);

        $this->templateFacade->saveCodeName($entity);
        $this->flashMessage('Kódové označení bylo uloženo.');

        if ($this->isAjax()) {
            $this->code_name_id = null; // Reset edit state
            $form->setValues([], true); // Clear form
            $this->template->codeNames = $this->templateFacade->getCodeNames($templateId);
            $this->redrawControl('codeNameFormSnippet');
            $this->redrawControl('codeNamesTableSnippet');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('edit#tab-code-names', ['id' => $templateId, 'code_name_id' => null]);
        }
    }
}
