<?php
/**
 * Created by PhpStorm.
 * User: PetrV
 * Date: 3.2.2019
 * Time: 10:35
 */

namespace App\Model\Base;

use App\Model\Base\IMapper;
use App\Model\Base\IEntity;

use Nette\SmartObject;

abstract class ADao
{

	use SmartObject;

	/** @var IMapper */
	protected IMapper $mapper;

	/** @var string */
	protected string $entityName;


	public function getEntity(string $entityName, array $data = [], ?string $lang = null): ?IEntity {

		$entity = null;

		try {
            if(str_contains($entityName, 'Modules')) {
                $entityPath = $entityName;
            }elseif (str_contains($entityName, '\\')) {
                $entityPath = 'App\Model\\' . $entityName;
            } else {
				$entityPath = 'App\Model\Entity\\' . $entityName;
			}
			$entity = new $entityPath($data);
		}catch(\Exception $e){

		}
		return 	$entity;
	}

	public function getEntityByTable(string $table, array $data = []): mixed {
		return null;
	}


	/**
	 * return all records from table
	 * @return IEntity[]|array
	 */
	public function findAll(?string $lang = null): array {
		$data = $this->mapper->findAll();
		if ($data) {
			$entities = [];
			foreach ($data as $key => $item) {
				$entities[$key] = $this->getEntity($this->entityName, (array) $item, $lang);
			}
			return $entities;
		}
		return [];
	}

	/**
	 * return filtered rows by array
	 * (array('name' => 'David') convert to SQL query WHERE name = 'David')
	 * @param array $by
	 * @return IEntity[]|array
	 */
	protected function findBy(array $by, ?string $lang = null): array {

		$data = $this->mapper->findBy($by);
		if ($data) {
			$entities = [];
			foreach ($data as $key => $item) {
				$entities[$key] = $this->getEntity($this->entityName, (array) $item, $lang);
			}
			return $entities;
		}
		return [];
	}

	/**
	 * return if record already exist
	 * (array('name' => 'David') convert to SQL query WHERE name = 'David')
	 * @param array $by
	 * @return bool
	 */
	public function rowExist(array $by): bool {
		return (bool) $this->mapper->rowExist($by);
	}

	/**
	 * return single row
	 * @param array $by
	 * @return IEntity|null
	 */
	public function findOneBy(array $by, ?string $orderBy = null): ?IEntity {
		$data = $this->mapper->findOneBy($by, $orderBy);
		if ($data) {
			return $this->getEntity($this->entityName, (array) $data);
		}
		return null;
	}

	/**
	 * return single row
	 * @param string $value name of col
	 * @param array $by
	 * @return mixed
	 */
	public function findOneValueBy(string $value, array $by): mixed {
		return $this->mapper->findOneValueBy($value, $by);
	}

	/**
	 * return ID of row
	 * @param array $by
	 * @return int|string|null
	 */
	public function getIDBy(array $by): mixed {
		return $this->mapper->getIDBy($by);
	}

	/**
	 * return all row by condition in array
	 * @param array $by
	 * @return IEntity[]|array
	 */
	public function findAllBy(array $by, ?int $limit = null, ?int $offset = null, ?string $orderBy = null, ?string $lang = null): array {
		$data = $this->mapper->findAllBy($by, $limit, $offset, $orderBy);
		if ($data) {
			$entities = [];
			foreach($data as $primaryKey => $values) {
				$entities[$primaryKey] = $this->getEntity($this->entityName, (array) $values, $lang);
			}
			return $entities;
		}
		return [];
	}

	/**
	 * return single row by primary key
	 * @param int|string $id
	 * @return IEntity|null
	 */
	public function find(mixed $id, ?string $lang = null): ?IEntity {

		$data = $this->mapper->find($id);
		if ($data) {
			return $this->getEntity($this->entityName, (array) $data, $lang);
		}
		return null;
	}



	/**
	 * count rows
	 * @param array $where if is not set count all
	 * @return int
	 */
	public function count(array $where = []): int {
		return $this->mapper->count($where);
	}

	/**
	 * count summary in rows
	 * @param string $field field name to summary
	 * @param array $where if is not set count all
	 * @return int|float
	 */
	public function sum(string $field, array $where = []): mixed {
		return $this->mapper->sum($field, $where);
	}

	/**
	 * update record
	 * @param array|IEntity $data array have to include primary key
	 * @return int|bool
	 */
	public function update(mixed $data): mixed {
		$ret = $this->mapper->update($data);
		$this->onUpdate($ret);
		return $ret;
	}

	/**
	 * insert record and return insert ID
	 * @param array|IEntity $data
	 * @return int|string|bool of inser ID
	 */
	public function insert(mixed $data): mixed {
		$ret = $this->mapper->insert($data);
		$this->onInsert($ret);
		return $ret;
	}

	/**
	 * remove row by ID
	 * @param int|string $id
	 */
	public function delete(mixed $id): void {
		$this->mapper->delete($id);
	}

	/**
	 * begin transaction
	 */
	public function begin(): void {
		$this->mapper->begin();
	}

	/**
	 * commit transaction
	 */
	public function commit(): void {
		$this->mapper->commit();
	}

	/**
	 * rollback transaction
	 */
	public function rollback(): void {
		$this->mapper->rollback();
	}

	/**
	 * get fable fields
	 * @return \Dibi\Result
	 */
	public function getTableFields(): \Dibi\Result {
		return $this->mapper->getTableFields();
	}




	/**
	 * @param string $entityName
	 * @param array $data
	 * @return IEntity[]|array
	 */
	public function getEntities(string $entityName, array $data = [], ?string $lang = null): array {
		if ($data) {
			$entities = [];
			foreach ($data as $key => $item) {

				$entities[$key] = $this->getEntity($entityName, (array) $item , $lang);
			}
			return $entities;
		}
		return [];
	}

	public function save(IEntity $entity): IEntity {
		return $this->mapper->save($entity);
	}

	protected function onUpdate(mixed $ret): void
	{
	}

	protected function onInsert(mixed $ret): void
	{
	}


}
