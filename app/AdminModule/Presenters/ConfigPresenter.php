<?php

namespace App\AdminModule\Presenters;

use App\Model\Config\ConfigFacade;
use App\Model\Config\ConfigEntity;
use App\Model\Lookup\LookupFacade;
use Nette\Application\UI\Form;

final class ConfigPresenter extends AdminPresenter
{
    /** @var ConfigFacade @inject */
    public $configFacade;

    /** @var LookupFacade @inject */
    public $lookupFacade;

    public function renderDefault(): void
    {
        $this->template->title = 'Nastavení systému';
        $this->template->configs = $this->configFacade->getAllConfigs();
    }

    public function renderEdit(?int $id = null): void
    {
        $this->template->title = $id ? 'Editace nastavení' : 'Nová nastavení';

        // Get languages excluding default CS
        $languages = $this->getOtherLanguages();
        $this->template->languages = $languages;

        if ($id) {
            $config = $this->configFacade->getConfig($id);
            if (!$config) {
                $this->error('Nastavení nebylo nalezeno');
            }

            $defaults = $config->getEntityData();
            foreach ($config->getTranslations() as $langId => $val) {
                $defaults['value_' . $langId] = $val;
            }

            $this['configForm']->setDefaults($defaults);
        }
    }

    public function actionDelete(int $id): void
    {
        $this->configFacade->deleteConfig($id);
        $this->flashMessage('Nastavení bylo smazáno.');
        $this->redirect('default');
    }

    protected function createComponentConfigForm(): Form
    {
        $form = new Form;
        $form->addHidden('config_id');

        $form->addText('item', 'Název (Klíč)')
            ->setRequired('Zadejte název nastavení');

        $form->addText('value', 'Hodnota (CS - Výchozí)')
            ->setRequired('Zadejte hodnotu');

        // Other values for each other language
        $languages = $this->getOtherLanguages();
        foreach ($languages as $lang) {
            $form->addText('value_' . $lang->lookup_id, 'Hodnota (' . strtoupper($lang->item) . ')');
        }

        $form->addSubmit('send', 'Uložit nastavení')
            ->setHtmlAttribute('class', 'btn btn-success');

        $form->onSuccess[] = $this->configFormSucceeded(...);
        return $form;
    }

    public function configFormSucceeded(Form $form, \stdClass $values): void
    {
        $config = new ConfigEntity();
        $config->fillEntity((array) $values);

        // Extract translations
        $translations = [];
        $languages = $this->getOtherLanguages();
        foreach ($languages as $lang) {
            $key = 'value_' . $lang->lookup_id;
            if (isset($values->$key)) {
                $translations[$lang->lookup_id] = $values->$key;
            }
        }
        $config->setTranslates($translations);

        $this->configFacade->saveConfig($config);
        $this->flashMessage('Nastavení bylo uloženo.');
        $this->redirect('default');
    }

    private function getOtherLanguages(): array
    {
        $all = $this->lookupFacade->getLookupList(500);
        return array_filter($all, fn($l) => $all[$l->lookup_id]->lookup_id != C_LANGUAGE_CS);
    }
}
