<?php

namespace App\UserModule\Forms;

use Nette;
use Nette\Application\UI\Form;
use Kdyby\Translation\Translator;

class FormFactory
{
	use Nette\SmartObject;
	
	/** @var Translator */
	private $translator;


	public function __construct(Translator $translator){
		$this->translator = $translator;	
	}
	
	public function create(): \App\UserModule\Forms\BaseForm
	{
		$form = new BaseForm;
		$form->setTranslator($this->translator);
		return $form;
	}

	/**
     * @param string $name
     *
     * @return Controls\NumberInput
     */
    public function addNumber($name, $label = NULL)
	{
		$control = new Controls\NumberInput($label);
		$control->setNullable();
		$control->setRequired(FALSE);

		return $this[$name] = $control;
	}



}
