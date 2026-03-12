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

	protected $defaultProperties = ['session_id','created_ip','created_dt','disabled_dt'];

	public $session_id;
	public $created_ip;
	public $created_dt;
	public $disabled_dt;

	/** @var array */
	protected $valuesSet = [];

	/** @var array */
	protected $valuesUpdated = [];

	/** @var bool */
	protected $isFilling = false;

	/** @var EncodeDecode */
	protected $encodeDecode;



	/**
	 * BaseEntity constructor.
	 * @param array $data
	 * @param bool|null $new  True if entity is new, False if loading from DB. If null, determined by presence of ID in $data.
	 */
	public function __construct($data = [], $new = null)
	{
		// Determine if new if not explicitly provided
		if ($new === null) {
			$new = true;
			// We check if any ID-like field is present in data (primary key)
			// Each entity should ideally define its primary key, but usually it ends with _id
			foreach ($data as $key => $val) {
				if (str_ends_with($key, '_id') && !empty($val)) {
					$new = false;
					break;
				}
			}
		}

		if($new){
			$this->setDefaultsProperty();
		}

		if ($data) {
			// If not new, we are hydrating from DB -> don't track as updated
			$this->fillEntity($data, false, !$new);
		}

	}

	public function get($variable, $format = null)
	{
		// Try snake_case first
		if (property_exists($this, $variable)) {
			$value = $this->$variable;
		} else {
			// Try converting camelCase to snake_case
			$snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $variable));
			if (property_exists($this, $snake)) {
				$value = $this->$snake;
				$variable = $snake;
			} else {
				return null;
			}
		}

		if ($value instanceof DateTime || $value instanceof \DateTime) {
			return $this->getDateTime($value, $format);
		}
		if (is_object($value) || is_array($value)) {
			return $this->getJSON($variable, $format);
		}
		return $value;
	}

	public function setId($id)
	{

	}

	public function getId()
	{

	}


	public function setDefaultsProperty(): void{

		foreach($this->defaultProperties as $property) {
			if (property_exists($this, $property)) {
				$methodName = 'set' . str_replace('_', '', ucwords((string) $property, '_'));
				if (method_exists($this, $methodName)) {
					$this->$methodName(null);
				}
			}
		}
	}

	public function setVariable($variableName, $value, $type = null) {
		if ($type && $value !== null) {
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
		
		if (!$this->isFilling) {
			$this->valuesUpdated[$variableName] = $variableName;
		}
		
		return $this;
	}


	/**
	 * get Entity and fill data
	 * @param array $data
	 * @param bool $setDefaults
	 * @param bool $isHydrating If true, changes won't be marked as updated
	 * @return IEntity
	 */
	public function fillEntity($data = [], $setDefaults = true, $isHydrating = false) {

		if ($setDefaults) {
            $this->setDefaultsProperty();
        }

		$prevFilling = $this->isFilling;
		$this->isFilling = $isHydrating;
		
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				$methodName = 'set' . str_replace('_', '', ucwords((string) $key, '_'));

				if (method_exists($this, $methodName)) {
                    $this->$methodName($value);
                } elseif (property_exists($this, $key)) {
                    $this->setVariable($key, $value);
                }
			}
		}
		$this->isFilling = $prevFilling;

		return $this;
	}

	/**
	 * Get all set data
	 * @return array
	 */
	public function getEntityData() {
		$values = [];
		foreach ($this->valuesSet as $value) {
				$getter = 'get' . str_replace('_', '', ucwords((string) $value, '_'));
				if (method_exists($this, $getter)) {
					$values[$value] = $this->$getter();
				} else {
					$values[$value] = ($this->$value === '') ? null : $this->$value;
				}
			}
		return $values;
	}

	/**
	 * Get only updated data
	 * @return array
	 */
	public function getUpdatedData() {
		$values = [];
		foreach ($this->valuesUpdated as $value) {
				$getter = 'get' . str_replace('_', '', ucwords((string) $value, '_'));
				if (method_exists($this, $getter)) {
					$values[$value] = $this->$getter();
				} elseif (property_exists($this, $value)) {
					$values[$value] = ($this->$value === '') ? null : $this->$value;
				}
			}
		return $values;
	}



	public function getJSON($variable, $key = false)
	{
		if (!isset($this->$variable) || $this->$variable === null) {
			return null;
		}

		if ($key === 'json' || $key === 'string') {
			return json_encode($this->$variable, JSON_UNESCAPED_UNICODE);
		}

		if ($key === 'array') {
			return json_decode(json_encode($this->$variable), true);
		}

		if ($key === true) {
			return $this->$variable;
		}

		if (is_string($key)) {
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
	 * @param string $format
	 * @return false|int|string|DateTime
	 */
	protected function getDateTime($value, $format) {
		if ($format == 'int' || $format === true) {
			if ($value instanceof DateTime) {
				return $value->getTimestamp();
			} elseif($value) {
				return strtotime((string) $value);
			}
		} elseif ($format == 'ms') {
			if ($value instanceof DateTime) {
				return $value->getTimestamp() * 1000;
			} elseif($value) {
				return strtotime((string) $value) * 1000;
			}
		} elseif ($format && $value instanceof DateTime) {

			return $value->format($format);
		}

		return $value;
	}

	/**
	 * @param DateTime|int|string $value
	 * @return DateTime
	 */
	protected function createDateTime($value)
	{
		if (is_numeric($value) || is_string($value)) {
            return new DateTime($value);
        }

		return $value;
	}



	protected function createJson($value) {

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
	public function getEncodeDecode()
	{
		if (!$this->encodeDecode) {
			$this->encodeDecode = new EncodeDecode();
		}
		return $this->encodeDecode;
	}



}
