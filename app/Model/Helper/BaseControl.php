<?php

namespace App\Model\Helper;

use Nette\Application\UI\Control;
use App\Model\Helper\ShortcodeService;

class BaseControl extends Control
{
    /** @var ShortcodeService */
    public $shortcodeService;

    public function injectShortcodeService(ShortcodeService $shortcodeService): void
    {
        $this->shortcodeService = $shortcodeService;
    }

    protected function createTemplate(?string $class = null): \Nette\Application\UI\Template
    {
        $template = parent::createTemplate();

        $template->addFilter('parseShortcodes', function ($content) {
            return $this->shortcodeService ? $this->shortcodeService->parse((string)$content) : $content;
        });

        $template->addFilter('renderString', function ($content) use ($template) {
            if (!$content) return '';
            $latte = $template->getLatte();
            $originalLoader = $latte->getLoader();
            $latte->setLoader(new \Latte\Loaders\StringLoader);

            try {
                $params = $this->getPresenter()->getTemplate()->getParameters();
                $result = $latte->renderToString($content, $params);
            } finally {
                $latte->setLoader($originalLoader);
            }

            if ($this->shortcodeService) {
                return $this->shortcodeService->parse($result);
            }
            
            return $result;
        });

        return $template;
    }
}
