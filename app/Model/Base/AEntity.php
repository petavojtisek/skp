<?php
/**
 * Created by PhpStorm.
 * User: PetrV
 * Date: 3.2.2019
 * Time: 10:16
 */

namespace App\Model\Base;

use App\Model\System\EncodeDecode;
use Dibi\DateTime;
use Nette\Utils\ArrayHash;

abstract class AEntity implements IEntity
{
	const VALUE_TYPE_INTEGER = 'int';
	const VALUE_TYPE_FLOAT = 'float';
	const VALUE_TYPE_STRING = 'string';
	const VALUE_TYPE_JSON = 'json';
	const VALUE_TYPE_DATE = 'date';
	const VALUE_TYPE_BOOLEAN = 'bool';

	protected array $defaultProperties = ['session_id','created_ip','created_dt'];

	public ?string $session_id = null;
	public ?string $created_ip = null;
	public mixed $created_dt = null;

	/** @var array */
	protected array $valuesSet = [];

	/** @var array */
	protected array $valuesUpdated = [];

	/** @var EncodeDecode|null */
	private ?EncodeDecode $encodeDecode = null;



	/**
	 * BaseEntity constructor.
	 * @param array $data
	 * @param bool $new
	 */
	public function __construct(array $data = [], bool $new = true)
	{
		if($new){
			$this->setDefaultsProperty();
		}

		if ($data) {
			$this->fillEntity($data, $new);
		}

	}

	public function setId(mixed $id): void
	{

	}

	public function getId(): mixed
	{
		return null;
	}


	public function setDefaultsProperty(): void
	{
		foreach($this->defaultProperties as $property) {
			if (property_exists($this, (string)$property)) {
				$methodName = 'set' . ucfirst((string)$property);
				if (method_exists($this, $methodName)) {
					$this->$methodName(null);
				}
			}
		}
	}

	public function setVariable(string $variableName, mixed $value, ?string $type = null): self
	{
		if ($type && $value) {
			switch ($type) {
				case self::VALUE_TYPE_INTEGER:
					$value = (int) $value;
					break;
				case self::VALUE_TYPE_BOOLEAN:
					$value = $value ? 1 : 0;
					break;
				case self::VALUE_TYPE_FLOAT:
					$value = (float) $value;
					break;
				case self::VALUE_TYPE_DATE:
					$value = $this->createDateTime($value);
					break;
				case self::VALUE_TYPE_STRING:
					$value = (string) $value;
					break;
				case self::VALUE_TYPE_JSON:
					$value = $this->createJson($value);
					break;
			}
		}

		$this->$variableName = $value;
		$this->valuesSet[$variableName] = $variableName;
		return $this;
	}


	/**
	 * get Entity and fill data
	 * @param array $data
	 * @param bool $setDefaults
	 * @return IEntity
	 */
	public function fillEntity(array $data = [], bool $setDefaults = true): IEntity
	{
		if($setDefaults) {
			$this->setDefaultsProperty();
		}

		if (!empty($data)) {
			foreach ($data as $key => $value) {
				$methodName = 'set' . ucfirst((string)$key);

				if (method_exists($this, $methodName)) {
					$this->$methodName($value);
				} else {
					if (property_exists($this, (string)$key)) {
						$this->setVariable((string)$key, $value);
					}
				}
			}
		}

		return $this;
	}

	/**
	 * Get core data
	 * @return array
	 */
	public function getEntityData(): array
	{
		$values = [];
		if (!empty($this->valuesSet)) {
			foreach ($this->valuesSet as $value) {
				$getter = 'get' . ucfirst((string)$value);
				if (method_exists($this, $getter)) {
					$values[$value] = ($this->$value === '') ? null : $this->$getter();
				} else {
					$values[$value] = ($this->$value === '') ? null : $this->$value;
				}
			}
		}
		return $values;
	}



	public function getJSON(string $variable, mixed $key = false): mixed
	{
		if (!isset($this->$variable) || !$this->$variable) {
			return null;
		}
		if ($key === true) {
			return $this->$variable;
		} elseif ($key === 'array') {
			return json_decode(json_encode($this->$variable), true);
		} elseif (is_string($key)) {
			if (isset($this->$variable->$key)) {
				return $this->$variable->$key;
			} else {
				return null;
			}
		}

		return json_encode($this->$variable, JSON_UNESCAPED_UNICODE);
	}


	/**
	 * @param DateTime|int|string $value
	 * @param string|bool $format
	 * @return false|int|string|DateTime
	 */
	protected function getDateTime(mixed $value, mixed $format): mixed
	{
		if ($format === 'int' || $format === true) {
			if ($value instanceof DateTime) {
				return $value->getTimestamp();
			} elseif($value) {
				return strtotime((string)$value);
			}
		} elseif ($format === 'ms') {
			if ($value instanceof DateTime) {
				return $value->getTimestamp() * 1000;
			} elseif($value) {
				return strtotime((string)$value) * 1000;
			}
		} elseif ($format && $value instanceof DateTime) {
			return $value->format((string)$format);
		}

		return $value;
	}

	/**
	 * @param DateTime|int|string $value
	 * @return DateTime|mixed
	 */
	protected function createDateTime(mixed $value): mixed
	{
		if (is_numeric($value) || is_string($value)) {
			return new DateTime($value);
		}

		return $value;
	}



	protected function createJson(mixed $value): mixed
	{
		if ($value === null) {
			return null;
		} elseif (is_array($value)) {
			return (object) $value;
		} elseif (is_string($value)) {
			return json_decode($value);
		} elseif ($value instanceof ArrayHash) {
			$value =  json_decode(json_encode($value));
			return (object) $value;
		} elseif (is_object($value)) {
			return $value;
		}
		return null;
	}

	/**
	 * @return EncodeDecode
	 */
	public function getEncodeDecode(): EncodeDecode
	{
		if (!$this->encodeDecode) {
			$this->encodeDecode = new EncodeDecode();
		}
		return $this->encodeDecode;
	}



}
