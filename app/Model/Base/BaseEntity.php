<?php
/**
 * Created by PhpStorm.
 * User: PetrV
 * Date: 3.2.2019
 * Time: 9:53
 */

namespace App\Model\Base;

use Dibi\DateTime;
use Nette\SmartObject;

class BaseEntity extends AEntity implements IEntity

{
	use SmartObject;


	function __call($method, $params)
	{

		$var = lcfirst(substr($method, 3));

		if (strncasecmp($method, "get", 3) === 0) {
			return $this->$var;
		}
		if (strncasecmp($method, "set", 3) === 0) {

			$this->setVariable($var,$params[0]);
		}
	}

	public function __get($property)
	{
		return (isset($this->$property)) ? $this->$property : null;
	}

	function __set($name, $value)
	{
		$type = self::VALUE_TYPE_STRING;

		if (is_int($value) or is_numeric($value)) {
			$type = self::VALUE_TYPE_INTEGER;
		}

		if (is_double($value) or is_float($value)) {
			$type = self::VALUE_TYPE_FLOAT;
		}

		if (is_array($value) or is_object($value) or json_decode($value)) {
			$type = self::VALUE_TYPE_JSON;
		}

		if ($value === true or $value === false) {
			$type = self::VALUE_TYPE_BOOLEAN;
		}

		if ($value instanceof \DateTime or $value instanceof DateTime or $value instanceof \Nette\Utils\DateTime) {
			$type = self::VALUE_TYPE_DATE;
		}

		$var = lcfirst($name);
		$this->setVariable($var, $value, $type);
	}

	public function setSessionId($sessionId = null)
	{
		if (!empty($sessionId)) {
			$this->setVariable('sessionId', $sessionId);
		} else {
			$this->setVariable('sessionId', session_id());
		}

	}

	public function getIdHash(){

		return $this->getEncodeDecode()->encodeSmallHash($this->getId());
	}

	public function setCreatedIp($createdIp = null)
	{
		if (!empty($createdIp)) {
			$this->setVariable('createdIp', $createdIp);
		} else {
			$this->setVariable('createdIp', filter_input(INPUT_SERVER, 'REMOTE_ADDR'));
		}
	}

	public function setCreatedDt($createdDt = null)
	{
		if (!empty($createdDt) and $createdDt instanceof DateTime) {
			$this->setVariable('createdDt', $createdDt);
		} else {
			$this->setVariable('createdDt', new DateTime());
		}

	}




}