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
	protected $mapper;

	/** @var string */
	protected $entityName;


	public function getEntity(string $entityName, $data = []) {

		$entity = null;

		try {
			if (str_contains($entityName, '\\')) {
				$entityPath = 'App\Model\\' . $entityName;
			} else {
				$entityPath = 'App\Model\\' . $entityName;
			}
			$entity = new $entityPath($data);
		}catch(\Exception){

		}
		return 	$entity;
	}

	public function getEntityByTable($table, $data = []){

	}


	/**
	 * return all records from table
	 * @return bool|IEntity[]
	 */
	public function findAll($lang = null) {
		$data = $this->mapper->findAll();
		if ($data) {
			$entities = [];
			foreach ($data as $key => $item) {
				$entities[$key] = $this->getEntity($this->entityName, (array) $item);
			}
			return $entities;
		}
		return false;
	}

	/**
     * return filtered rows by array
     * (array('name' => 'David') convert to SQL query WHERE name = 'David')
     * @return bool|IEntity[]
     */
    protected function findBy(array $by) {

		$data = $this->mapper->findBy($by);
		if ($data) {
			$entities = [];
			foreach ($data as $key => $item) {
				$entities[$key] = $this->getEntity($this->entityName, (array) $item);
			}
			return $entities;
		}
		return false;
	}

	/**
     * return if record already exist
     * (array('name' => 'David') convert to SQL query WHERE name = 'David')
     * @return bool
     */
    public function rowExist(array $by) {
		return (bool) $this->mapper->rowExist($by);
	}

	/**
     * return single row
     * @return bool|IEntity
     */
    public function findOneBy(array $by, $orderBy = null) {
		$data = $this->mapper->findOneBy($by, $orderBy);
		if ($data) {
			return $this->getEntity($this->entityName, (array) $data);
		}
		return false;
	}

	/**
     * return single row
     * @param string $value name of col
     * @return mixed
     */
    public function findOneValueBy($value, array $by) {
		return $this->mapper->findOneValueBy($value, $by);
	}

	/**
     * return ID of row
     * @return int
     */
    public function getIDBy(array $by) {
		return $this->mapper->getIDBy($by);
	}

	/**
     * return all row by condition in array
     * @return bool|IEntity[]
     */
    public function findAllBy(array $by, $limit = null, $offset = null, $orderBy = null, $lang = null) {
		$data = $this->mapper->findAllBy($by, $limit, $offset, $orderBy);
		if ($data) {
			$entities = [];
			foreach($data as $primaryKey => $values) {
				$entities[$primaryKey] = $this->getEntity($this->entityName, (array) $values);
			}
			return $entities;
		}
		return false;
	}

	/**
	 * return single row by primary key
	 * @param int $id
	 * @return bool|IEntity
	 */
	public function find($id, $lang = null) {

		$data = $this->mapper->find($id);
		if ($data) {
			return $this->getEntity($this->entityName, (array) $data);
		}
		return false;
	}

	/**
	 * count rows
	 * @param array $where if is not set count all
	 * @return int
	 */
	public function count($where = []) {
		return $this->mapper->count($where);
	}

	/**
	 * count summary in rows
	 * @param string $field field name to summary
	 * @param array $where if is not set count all
	 * @return int
	 */
	public function sum($field, $where = []) {
		return $this->mapper->sum($field, $where);
	}

	/**
	 * update record
	 * @param array|IEntity $data array have to include primary key
	 */
	public function update($data) {
		$ret = $this->mapper->update($data);
		$this->onUpdate($ret);
		return $ret;
	}

	/**
	 * insert record and return insert ID
	 * @param array|IEntity $data
	 * @return int of inser ID
	 */
	public function insert($data) {
		$ret = $this->mapper->insert($data);
		$this->onInsert($ret);
		return $ret;
	}

	/**
	 * remove row by ID
	 * @param int $id
	 */
	public function delete($id): void {
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
	public function getTableFields() {
		return $this->mapper->getTableFields();
	}

	public function getMapper(): IMapper
	{
		return $this->mapper;
	}


	/**
	 * @param $entityName
	 * @param array $data
	 * @return IEntity[]|bool
	 */
	public function getEntities($entityName, $data = [], $lang = null) {
		if ($data) {
			$entities = [];
			foreach ($data as $key => $item) {
				$entities[$key] = $this->getEntity($entityName, (array) $item);
			}
			return $entities;
		}
		return false;
	}

	protected function onUpdate($ret)
	{
	}

	protected function onInsert($ret)
	{
	}


}
