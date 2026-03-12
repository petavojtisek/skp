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


	function __call(string $method, array $params)
	{

		$var = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', substr($method, 3)));

		if (strncasecmp($method, "get", 3) === 0) {
			return $this->$var;
		}
		if (strncasecmp($method, "set", 3) === 0) {

			$this->setVariable($var,$params[0]);
		}
	}

	public function __get(string $property): mixed
	{
		return $this->$property ?? null;
	}

	public function __isset(string $property): bool
	{
		return isset($this->$property);
	}

	function __set(string $name, mixed $value)
	{
		$type = self::VALUE_TYPE_STRING;

		if (is_int($value) or is_numeric($value)) {
			$type = self::VALUE_TYPE_INTEGER;
		}

		if (is_float($value) or is_float($value)) {
			$type = self::VALUE_TYPE_FLOAT;
		}

		if (is_array($value) or is_object($value) or json_decode((string) $value)) {
			$type = self::VALUE_TYPE_JSON;
		}

		if ($value === true or $value === false) {
			$type = self::VALUE_TYPE_BOOLEAN;
		}

		if ($value instanceof \DateTime or $value instanceof DateTime or $value instanceof \Nette\Utils\DateTime) {
			$type = self::VALUE_TYPE_DATE;
		}

		$var = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
		$this->setVariable($var, $value, $type);
	}

	public function setSessionId($sessionId = null): void
	{
		if (!empty($sessionId)) {
			$this->setVariable('session_id', $sessionId);
		} else {
			$this->setVariable('session_id', session_id());
		}

	}

	public function getIdHash(){

		return $this->getEncodeDecode()->encodeSmallHash($this->getId());
	}

	public function setCreatedIp($createdIp = null): void
	{
		if (!empty($createdIp)) {
			$this->setVariable('created_ip', $createdIp);
		} else {
			$this->setVariable('created_ip', filter_input(INPUT_SERVER, 'REMOTE_ADDR'));
		}
	}

	public function setCreatedDt($createdDt = null): void
	{
		if (!empty($createdDt) and $createdDt instanceof DateTime) {
			$this->setVariable('created_dt', $createdDt);
		} else {
			$this->setVariable('created_dt', new DateTime());
		}

	}




}