<?php

namespace App\UserModule\Forms;

use Kdyby\Translation\Translator;
use Nette\Forms\Controls;
use Nette\Forms\Form;

class BaseForm extends \Nette\Application\UI\Form
{
	/** @var $isAjax */
	private $isAjax = false;

	/** @var callable */
	public $onParentSuccess;

	/** @var callable */
	public $onParentError;

	/** @var Translator */
	private $translator;

	/**
	 * Set form ajax flag
	 * @param bool $isAjax
	 * @return BaseForm
	 */
	public function setAjax($isAjax)
	{
		$this->isAjax = $isAjax;
		return $this;
	}

	/**
	 * Returns isAjax value
	 * @return bool
	 */
	public function isAjax()
	{
		return $this->isAjax;
	}

	/**
	 * @param callable $callback
	 * @return $this
	 */
	public function setParentSuccessCallback(callable $callback){
		$this->onParentSuccess[] = $callback;
		return $this;
	}

	/**
	 * @param callable $callback
	 * @return $this
	 */
	public function setParentErrorCallback(callable $callback){
		$this->onParentError[] = $callback;
		return $this;
	}

	/**
	 * @return void
	 */
	protected function beforeRender()
	{

		$classes  = $this->getElementPrototype()->getClass();
		$classes  = (empty($classes) ? array() : array_unique(explode(" ", $classes)));

		$position = array_search("ajax", $classes);

		if($position === FALSE and $this->isAjax)
		{
			$classes[] = "ajax";
		}
		else if($position !== FALSE and !$this->isAjax)
		{
			file_put_contents(DIR_LOG.DS."ajaxform-need-factory.log", "Je potreba nastavit ajax u formulare ".$this->getName().", ktery je potomkem ".$this->getParent()->getName()."\r\n", FILE_APPEND);
			array_splice($classes, $position, 1);
		}

		$this->getElementPrototype()->setClass(implode(" ", $classes));

		parent::beforeRender();
	}

	/**
	 * Adds input for email.
	 * @param  string  control name
	 * @param  string  label
	 * @return Controls\TextInput
	 */
	public function addEmail($name, $label = NULL)
	{
		return $this[$name] = (new Controls\TextInput($label))
			->setOption('type', 'email')
			->setRequired(FALSE)
			->addRule(Form::EMAIL,'messages.validate.email');
	}

	/**
	 * @param string $name
	 * @param null   $label
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

	/**
	 * @param string $name
	 * @param null   $label
	 *
	 * @return Controls\MonthInput
	 */
	public function addMonth($name, $label = NULL)
	{
		$control = new Controls\MonthInput($label);
		$control->setNullable();
		$control->setRequired(FALSE);

		return $this[$name] = $control;
	}

	/**
	 * @param string $name
	 * @param null   $label
	 *
	 * @return Controls\DateInput
	 */
	public function addDate($name, $label = NULL)
	{
		$control = new Controls\DateInput($label);
		$control->setNullable();
		$control->setRequired(FALSE);

		return $this[$name] = $control;
	}

	/**
	 * @param string $name
	 * @param null   $label
	 *
	 * @return Controls\DateTimeInput
	 */
	public function addDateTime($name, $label = NULL)
	{
		$control = new Controls\DateTimeInput($label);
		$control->setNullable();

		return $this[$name] = $control;
	}



}