<?php

namespace App\AdminModule\Presenters;

use App\Model\Lookup\LookupFacade;
use App\Model\Lookup\LookupEntity;
use Nette\Application\UI\Form;

final class LookupPresenter extends AdminPresenter
{
    /** @var LookupFacade @inject */
    public $lookupFacade;

    public function renderDefault(): void
    {
        $this->template->title = 'Číselníky';
        $this->template->tree = $this->lookupFacade->getLookupTree();
    }

    public function renderEdit(?int $id = null, ?int $parentId = null): void
    {
        if ($id === 1) {
            $this->flashMessage('Systémový kořen nelze editovat.', 'danger');
            $this->redirect('default');
        }
        $this->template->title = $id ? 'Editace položky' : 'Nová položka';
        
        // Get languages for translations (excluding CS)
        $languages = $this->lookupFacade->getLookupList(500);
        $this->template->languages = array_filter($languages, fn($l) => $l->lookup_id != C_LANGUAGE_CS);

        if ($id) {
            $lookup = $this->lookupFacade->getLookup($id);
            if (!$lookup) $this->error('Položka nebyla nalezena');
            
            $defaults = $lookup->getEntityData();
            foreach ($lookup->getTranslations() as $langId => $val) {
                $defaults['item_' . $langId] = $val;
            }
            $this['lookupForm']->setDefaults($defaults);
        } elseif ($parentId) {
            $this['lookupForm']->setDefaults(['parent_id' => $parentId]);
        }
    }

    public function actionDelete(int $id): void
    {
        if ($id === 1) {
            $this->flashMessage('Systémový kořen nelze smazat.', 'danger');
            $this->redirect('default');
        }
        $this->lookupFacade->deleteLookup($id);
        $this->flashMessage('Položka byla smazána.');
        $this->redirect('default');
    }

    protected function createComponentLookupForm(): Form
    {
        $form = new Form;
        $form->addHidden('lookup_id');
        $form->addHidden('parent_id')->setDefaultValue(1);

        $form->addText('item', 'Název (CS - Výchozí)')
            ->setRequired('Zadejte název');

        $form->addText('constant', 'Konstanta (nepovinné)');

        // Translation fields
        $languages = $this->lookupFacade->getLookupList(500);
        foreach ($languages as $lang) {
            if ($lang->lookup_id == C_LANGUAGE_CS) continue;
            $form->addText('item_' . $lang->lookup_id, 'Název (' . strtoupper($lang->item) . ')');
        }

        $form->addSubmit('send', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-success');

        $form->onSuccess[] = $this->lookupFormSucceeded(...);
        return $form;
    }

    public function lookupFormSucceeded(Form $form, \stdClass $values): void
    {
        $lookup = new LookupEntity();
        $lookup->fillEntity((array) $values);
        
        // Extract translations
        $translations = [];
        $languages = $this->lookupFacade->getLookupList(500);
        foreach ($languages as $lang) {
            $key = 'item_' . $lang->lookup_id;
            if (isset($values->$key)) {
                $translations[$lang->lookup_id] = $values->$key;
            }
        }
        $lookup->setTranslations($translations);

        $this->lookupFacade->saveLookup($lookup);
        $this->flashMessage('Položka byla uložena.');
        $this->redirect('default');
    }
}
