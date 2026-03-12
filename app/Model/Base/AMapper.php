<?php
/**
 * Created by PhpStorm.
 * User: PetrV
 * Date: 3.2.2019
 * Time: 10:17
 */

namespace App\Model\Base;

use Dibi\Fluent;
use Nette;
use Dibi\Connection;
use App\Model\Base\IEntity;

abstract class AMapper implements IMapper
{

	use Nette\SmartObject;


	protected \Dibi\Connection $db;

	/** @var Connection */
	protected $dbSlave;

	/** @var string */
	protected $tableName;

	/** @var string */
	protected $primaryKey;

	/** @var array */
	private static $tableColumns = [];

	/** @var \Nette\DI\Container */
	protected $container;


	public function __construct(Connection $connection) {
		$this->db = $connection;
	}

	public function injectContainer(\Nette\DI\Container $container)
	{
		$this->container = $container;
	}

	public function setSlaveConnection(Connection $slaveConnection): void{
		$this->dbSlave = $slaveConnection;
	}

	public function begin(): void{
		$this->db->begin();
	}

	public function commit(): void{
		$this->db->commit();
	}

	public function rollback(): void {
		$this->db->rollback();
	}

	public function findAll() {
		return $this->db->select('*')->from($this->tableName)->fetchAssoc($this->primaryKey);
	}

	public function findBy(array $by, $orderBy = null) {
		$query = $this->db->select('*')->from($this->tableName)->where($by);
		if ($orderBy) {
			$query->orderBy($orderBy);
		}
		return $query;
	}

	public function rowExist(array $by) {
		return (bool) $this->db->select(1)->from($this->tableName)->where($by)->fetchSingle();
	}

	public function findOneBy(array $by, $orderBy = null) {
		return $this->findBy($by,$orderBy)->fetch();
	}

	public function findOneValueBy($value, array $by) {
		return $this->db->select($value)->from($this->tableName)->where($by)->fetchSingle();
	}

	public function getIDBy(array $by) {
		return $this->db->select($this->primaryKey)->from($this->tableName)->where($by)->fetchSingle();
	}

	public function getIDsBy(array $by) {
		return $this->db->select($this->primaryKey)->from($this->tableName)->where($by)->fetchPairs($this->primaryKey, $this->primaryKey);
	}

	public function findAllBy($by = [], $limit = null, $offset = null, $orderBy = null) {
		$query = $this->db->select('*')->from($this->tableName);
		if ($by) {
			$query->where($by);
		}
		if ($limit) {
			$query->limit($limit);
		}
		if ($offset) {
			$query->offset((int)$offset);
			$query->limit((int)$limit);
		}
		if ($orderBy) {
			$query->orderBy($orderBy);
		}
		return $query->fetchAssoc($this->primaryKey);
	}

	public function find($id) {
		return $this->db->select('*')->from($this->tableName)->where([$this->primaryKey => $id])->fetch();
	}

	public function count($where = []) {
		return $this->db->select('COUNT(*)')->from($this->tableName)->where($where)->fetchSingle();
	}

	public function sum(string $field, $where = []) {
		return $this->db->select('SUM(' . $field . ')')->from($this->tableName)->where($where)->fetchSingle();
	}

	public function update($data) {
		$q = $this->getUpdateQuery($data);
		if ($q) {
			return $this->doUpdate($q);
		}
		return 0;
	}

	private function getUpdateQuery(array|IEntity $data){
		$id = null;
		$updateData = [];

		if ($data instanceof IEntity) {
			$id = $data->getId();
			$updateData = $data->getUpdatedData();
		} elseif (is_array($data) && isset($data[$this->primaryKey])) {
			$id = $data[$this->primaryKey];
			$updateData = $data;
			unset($updateData[$this->primaryKey]);
		}

		if ($id && !empty($updateData)) {
			$dbData = $this->filterColumns($updateData);
			if (!empty($dbData)) {
				return $this->db->update($this->tableName, $dbData)->where($this->primaryKey . ' = %i', $id);
			}
		}
		return 0;
	}

	public function insert($data) {
		$insertData = [];
		if ($data instanceof IEntity) {
			$insertData = $data->getEntityData();
		} elseif(is_array($data)){
			$insertData = $data;
		}

		if (!empty($insertData)) {
			$dbData = $this->filterColumns($insertData);
			$this->db->insert($this->tableName, $dbData)->execute();
			$lastInsertId = $this->db->getInsertId();

			if ($data instanceof IEntity) {
				$data->setId($lastInsertId);
			}
			return $lastInsertId;
		}
		return false;
	}

	/**
	 * Filters data to include only existing table columns
	 */
	private function filterColumns(array $data): array
	{
		$columns = $this->getTableColumns();
		return array_intersect_key($data, array_flip($columns));
	}

	/**
	 * Returns array of column names for current table
	 */
	private function getTableColumns(): array
	{
		if (!isset(self::$tableColumns[$this->tableName])) {
			self::$tableColumns[$this->tableName] = $this->db->query('SHOW COLUMNS FROM ' . $this->tableName)->fetchPairs('Field', 'Field');
		}
		return self::$tableColumns[$this->tableName];
	}

	public function delete($id) {
		$this->db->delete($this->tableName)->where($this->primaryKey . ' = %i', $id)->execute();
		return $this->db->getAffectedRows();
	}

	public function deleteBy($by) {
		$this->db->delete($this->tableName)->where($by)->execute();
		return $this->db->getAffectedRows();
	}

	public function assocArray($key, $array) {
		$newArray = [];
		if (!empty($array)) {
			foreach ($array as $value) {
				$newArray[$value->$key][] = $value;
			}
		}
		return $newArray;
	}

	public function getTableFields() {
		return $this->db->query('SHOW FIELDS FROM ' . $this->tableName . '');
	}

	public function save(IEntity $entity, $withTranslation = false) {
		if ($entity->getId()) {
			$this->update($entity);
		} else {
			$id = $this->insert($entity);
			$entity->setId($id);
		}
		return $entity;
	}

	public function getLookupOptions($pid, $query = false){
		$rs =  $this->db->select('*')->from('lookup')->where('pid=%i',$pid);
		if($query){
			$rs->where('item LIKE %like~',$query);
		}
		return $rs->fetchPairs('id','item');
	}

	public function getLookupList($pid){
		return $this->db->select('*')->from('lookup')->where('pid=%i',$pid)->fetchAssoc('id');
	}

	public function getLookupItem($lookupId){
		return $this->db->select('item')->from('lookup')->where('id=%i',$lookupId)->fetchSingle();
	}

	private function doUpdate(Fluent $q): int{
		$q->execute();
		return $this->db->getAffectedRows();
	}
}
