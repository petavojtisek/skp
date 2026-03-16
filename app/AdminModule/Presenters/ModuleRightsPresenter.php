<?php

namespace App\AdminModule\Presenters;

use App\Model\AdminGroup\AdminGroupFacade;
use App\Model\ModuleRights\ModuleRightsFacade;
use Nette\Application\UI\Form;

final class ModuleRightsPresenter extends AdminPresenter
{
    /** @inject */
    public AdminGroupFacade $groupFacade;

    /** @inject */
    public ModuleRightsFacade $moduleRightsFacade;

    /** @persistent */
    public ?int $id;

    public function actionDefault(): void
    {
        $this->id = null;
    }

    public function renderDefault(): void
    {
        $this->template->title = 'Práva modulů';
        $this->template->rights = $this->moduleRightsFacade->getRights();
    }

    public function renderEdit(?int $id = null): void
    {
        $this->template->title = $id ? 'Editace oprávnění' : 'Nové oprávnění';

        $groups = [];
        foreach ($this->groupFacade->getGroups() as $g) {
            $groups[$g->admin_group_id] = $g->admin_group_name;
        }
        $this['rightsForm']['admin_group_id']->setItems($groups);

        // Skeleton
    }

    public function actionDelete(int $id): void
    {
        // Skeleton
        $this->flashMessage('Oprávnění bylo smazáno.');
        $this->redirect('default');
    }

    protected function createComponentRightsForm(): Form
    {
        $form = new Form;
        $form->addHidden('rights_id');
        $form->addSelect('admin_group_id', 'Skupina')
            ->setPrompt('Zvolte skupinu')
            ->setRequired('Zvolte skupinu');
        $form->addText('module_name', 'Název modulu')
            ->setRequired('Zadejte název modulu');
        $form->addText('action_name', 'Název akce');
        $form->addCheckbox('is_allowed', 'Povoleno')->setDefaultValue(true);

        $form->addSubmit('send', 'Uložit');
        $form->onSuccess[] = [$this, 'rightsFormSucceeded'];
        return $form;
    }

    public function rightsFormSucceeded(Form $form, \stdClass $values): void
    {
        // Skeleton
        $this->flashMessage('Oprávnění bylo uloženo.');
        $this->redirect('default');
    }
}
