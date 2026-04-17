<?php

namespace App\FrontModule\Presenters\Error;

use Nette;
use Nette\Application\BadRequestException;

final class Error4xxPresenter extends Nette\Application\UI\Presenter
{
	public function startup(): void
	{
		parent::startup();
		if (!$this->getRequest()->isMethod(Nette\Application\Request::FORWARD)) {
			$this->error();
		}
	}

	public function renderDefault(BadRequestException $exception): void
	{
		// renders 4xx.latte or 404.latte etc
		$code = $exception->getCode();
		$file = __DIR__ . "/../../templates/Error/$code.latte";
		$this->template->setFile(is_file($file) ? $file : __DIR__ . '/../../templates/Error/4xx.latte');
        $this->template->code = $code;
	}
}
