<?php

namespace App\Model\Helper;

use Nette\Application\UI\Control;

class BaseControl extends Control
{
    protected function createTemplate(?string $class = null): \Nette\Application\UI\Template
    {
        $template = parent::createTemplate();

        $template->addFilter('renderString', function ($content) use ($template) {
            if (!$content) return '';
            $latte = $template->getLatte();
            $originalLoader = $latte->getLoader();
            $latte->setLoader(new \Latte\Loaders\StringLoader);

            try {
                // Tady bacha: $template->getParameters() v controlu
                // nemusí obsahovat globální proměnné z presenteru!
                // Musíš je tam buď poslat, nebo vzít z presenteru:
                $params = $this->getPresenter()->getTemplate()->getParameters();
                $result = $latte->renderToString($content, $params);
            } finally {
                $latte->setLoader($originalLoader);
            }
            return $result;
        });

        return $template;
    }
}
