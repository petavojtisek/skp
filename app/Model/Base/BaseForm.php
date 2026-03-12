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

	/**
     * Set form ajax flag
     * @param bool $isAjax
     */
    public function setAjax($isAjax): static
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
     * @return $this
     */
    public function setParentSuccessCallback(callable $callback): static{
		$this->onParentSuccess[] = $callback;
		return $this;
	}

	/**
     * @return $this
     */
    public function setParentErrorCallback(callable $callback): static{
		$this->onParentError[] = $callback;
		return $this;
	}

	protected function beforeRender(): void
	{

		$classes  = $this->getElementPrototype()->getClass();
		$classes  = (empty($classes) ? [] : array_unique(explode(" ", $classes)));

		$position = array_search("ajax", $classes);

		if ($position === FALSE && $this->isAjax) {
            $classes[] = "ajax";
        } elseif ($position !== FALSE && !$this->isAjax) {
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
     */
    public function addEmail($name, $label = NULL, int $maxLength = 255): \Nette\Forms\Controls\TextInput
	{
		return $this[$name] = (new Controls\TextInput($label))
			->setOption('type', 'email')
			->setRequired(FALSE)
			->addRule(Form::EMAIL,'messages.validate.email');
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

	/**
     * @param string $name
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
     *
     * @return Controls\DateInput
     */
    public function addDate($name, $label = NULL): \Nette\Forms\Controls\DateTimeControl
	{
		$control = new Controls\DateInput($label);
		$control->setNullable();
		$control->setRequired(FALSE);

		return $this[$name] = $control;
	}

	/**
     * @param string $name
     *
     * @return Controls\DateTimeInput
     */
    public function addDateTime($name, $label = NULL, bool $withSeconds = false): \Nette\Forms\Controls\DateTimeControl
	{
		$control = new Controls\DateTimeInput($label);
		$control->setNullable();

		return $this[$name] = $control;
	}



}