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

    protected function createTemplate(?string $class = null): \Nette\Application\UI\Template
    {
        $template = parent::createTemplate();
        $template->addFilter('renderString', function ($content) use ($template) {
            $latte = $template->getLatte(); // Získáme už existující engine
            // Použijeme stávající parametry, které už v template jsou
            return $latte->renderToString($content, $template->getParameters());
        });
        return $template;
    }
}
