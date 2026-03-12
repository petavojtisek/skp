<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Dibi\Connection;

use App\Model\Lookup\LookupFacade;

abstract class BasePresenter extends Presenter
{
    /** @var Connection @inject */
    public $connection;

    /** @var LookupFacade @inject */
    public $lookupFacade;

    public function startup(): void
    {
        parent::startup();
        
        $this->initConstants();
    }

    protected function initConstants(): void
    {
        $constants = $this->lookupFacade->getConstants();
        foreach ($constants as $name => $id) {
            $constName = 'C_' . strtoupper((string) $name);
            if (!defined($constName)) {
                define($constName, $id);
            }
            $this->template->$constName = $id;
        }
    }
}
