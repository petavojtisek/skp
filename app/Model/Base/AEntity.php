<?php
namespace App\Model\Base;


use Dibi\DateTime;
use Dibi\Row;
use Nette\Utils\ArrayHash;

abstract class AEntity implements IEntity
{
	const string VALUE_TYPE_INTEGER = 'int';
	const string VALUE_TYPE_FLOAT = 'float';
	const string VALUE_TYPE_STRING = 'string';
	const string VALUE_TYPE_JSON = 'json';
	const string VALUE_TYPE_DATE = 'date';
	const string VALUE_TYPE_BOOLEAN = 'bool';

	protected array $defaultProperties = ['session_id','created_ip','created_dt'];

	public ?string $session_id = null;
	public ?string $created_ip = null;
	public mixed $created_dt = null;

	/** @var array */
	protected array $valuesSet = [];

    protected $valuesIgnored = [];

    protected $valuesDiff = [];

    /** @var array */
	protected array $valuesUpdated = [];


    /** @var array of BaseTranslateEntity */
    protected array $translates = [];

	/**
	 * BaseEntity constructor.
	 * @param array $data
	 * @param bool $new
	 */
	public function __construct(array|Row|\ArrayObject $data = [], bool $new = true)
	{

        if($data instanceof Row){
            $data = (array) $data;
        }

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
		if ($type and $value) {
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

        if (!empty($this->$variableName) and $this->$variableName !== $value) {
            $this->valuesUpdated[$variableName] = $variableName;
        }

		$this->$variableName = $value;
		$this->valuesSet[$variableName] = $variableName;


        // Sleduj zmeny v entitach
        $this->logChanges($variableName, $type === self::VALUE_TYPE_JSON ? json_encode($value) : $value);

        return $this;
	}

    protected function logChanges(string $variableName, $value): void
    {
        if (!in_array($variableName, $this->valuesIgnored, true))
        {
            if (array_key_exists($variableName, $this->valuesDiff) and is_array($this->valuesDiff[$variableName]) and count($this->valuesDiff[$variableName]) > 0)
            {
                if ($this->valuesDiff[$variableName][0] != $value) // Zemerne pouzita podobnost ( != ) namisto totoznosti ( !== )
                {
                    $this->valuesDiff[$variableName][1] = $value;
                }
                else if (array_key_exists(1, $this->valuesDiff[$variableName]))
                {
                    unset($this->valuesDiff[$variableName][1]);
                }
            }
            else
            {
                $this->valuesDiff[$variableName] = [$value];
            }
        }
    }


	/**
	 * get Entity and fill data
	 * @param array $data
	 * @param bool $setDefaults
	 * @return IEntity
	 */
    public function fillEntity(array $data = [], bool $setDefaults = true, array $languages = []): IEntity
    {
        if ($setDefaults) {
            $this->setDefaultsProperty();
        }

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if ($key == 'translates') {
                    foreach ($value as $translateKey => $translateValue) {
                        if ($translateValue instanceof BaseTranslateEntity) {
                            $this->addTranslate($translateValue);
                        } else {
                            $translateValue = new \ArrayObject($translateValue);
                            $translate = new BaseTranslateEntity([
                                'element_id' => $translateValue->element_id?? null,
                                'lang_id' => $translateValue->lang_id?? null,
                                'value' => $translateValue->value?? null,
                            ]);

                            $this->addTranslate($translate);
                        }
                    }
                } else {

                    $methodName = 'set' . ucfirst((string)$key);
                    $pascalCase = str_replace('_', '', ucwords($key, '_'));
                    $methodName2 = 'set' .$pascalCase;
                    if (method_exists($this, $methodName)) {
                        $this->$methodName($value);
                    }elseif (method_exists($this, $methodName2)) {
                            $this->$methodName2($value);
                    } else {
                        if (property_exists($this, (string)$key)) {
                            $this->setVariable((string)$key, $value);
                        }
                    }
                }
            }
        }


        if(!empty($languages) and !empty($data)){
            $this->createTranslates($languages, $data);
        }

        return $this;
    }


    public function setTranslates(array $translates): void
    {

        $this->translates = $translates;
    }

    public function getTranslates(): array
    {
        return $this->translates;
    }

    public function getTranslate(int $langId)
    {
        return $this->translates[$langId] ?? new BaseTranslateEntity();
    }

    public function addTranslate(BaseTranslateEntity $translateEntity): void
    {
        $this->translates[$translateEntity->getLangId()] = $translateEntity;
    }


    public function createTranslates($languages, $values)
    {
        // Extract translations
        $translations =  $this->getTranslates();
        foreach ($languages as $lang) {
            $key = 'item_' . $lang->lookup_id;

            if (isset($values[$key]) and $lang->lookup_id !== C_LANGUAGE_CS) {
                $translateEntity = $this->getTranslate($lang->lookup_id);
                $translateEntity->fillEntity([
                    'entity_id' => (int)$this->getId(),
                    'lang_id' => $lang->lookup_id,
                    'value' => $values[$key]
                ]);
                $translations[$lang->lookup_id] = $translateEntity;
            }
        }
        $this->setTranslates($translations);
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

    public function getUpdatedData() {
        $values = array();
        if (!empty($this->valuesUpdated)) {
            foreach ($this->valuesUpdated as $value) {
                $getter = 'get' . ucfirst($value);
                $values[$value] = ($this->$value === '') ? null : $this->$getter();
            }
        }
        return $values;
    }

    public function hasChanged($includeObjects = false) : bool
    {
        foreach ($this->valuesDiff as $values)
            if (count($values) > 1 and ($includeObjects or ((!is_object($values[0]) or method_exists($values[0], '__toString')) and (!is_object($values[1]) or method_exists($values[1], '__toString')))))
                return true;

        return false;
    }

    public function getDiffData(bool $allValues = false, bool $includeObjects = false) : ?array
    {
        $diff = null;

        if (count($this->valuesDiff) > 0)
        {
            $diff = [
                'before' => [],
                'after' => [],
            ];

            foreach ($this->valuesDiff as $name => $values)
            {
                if (!$includeObjects)
                {
                    foreach ([0,1] as $i)
                    {
                        if (array_key_exists($i, $values) and is_object($values[$i]))
                        {
                            if (method_exists($values[$i], '__toString'))
                            {
                                $values[$i] = $values[$i]->__toString();
                            }
                            else
                            {
                                unset($values[$i]);
                            }
                        }
                    }
                }

                if (count($values) > 0)
                {
                    if (count($values) === 2 and ($includeObjects or (!is_object($values[0]) and !is_object($values[1]))))
                    {
                        $diff['before'][$name] = $values[0];
                        $diff['after'][$name] = $values[1];
                    }
                    else if ($allValues and ($includeObjects or !is_object($values[0])))
                    {
                        $diff['before'][$name] = $values[0];
                        $diff['after'][$name] = $values[0];
                    }
                }
            }
        }

        return $diff;
    }

	public function getJSON(string $variable, mixed $key = false): mixed
	{
		if (!isset($this->$variable) or !$this->$variable) {
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
		if ($format === 'int' or $format === true) {
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
		} elseif ($format and $value instanceof DateTime) {
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
		if (is_numeric($value) or is_string($value)) {
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
}
