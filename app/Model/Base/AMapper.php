<?php
/**
 * Created by PhpStorm.
 * User: PetrV
 * Date: 3.2.2019
 * Time: 10:17
 */

namespace App\Model\Base;

use Dibi\Fluent;
use Latte\IMacro;
use Nette;
use Dibi\Connection;
use Dibi\Result;
use Dibi\Row;
use App\Model\Base\IEntity;

abstract class AMapper implements IMapper
{

	use Nette\SmartObject;


	/** @var Connection */
	protected Connection $db;

	/** @var Connection */
	protected Connection $dbSlave;

	/** @var string */
	protected string $tableName;

	/** @var string */
	protected string $primaryKey;


    public string $translateTableName;
    public string $translatePrimaryKey;
    public string $translateLangId = 'lang_id';
    public string $translateValueKey = 'value';

    /** @var \Nette\DI\Container */
    protected \Nette\DI\Container $container;


	public function __construct(Connection $connection) {
		$this->db = $connection;
		#$this->dbSlave = $slaveConnection;
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

	public function findAll(): array {
		return $this->db->select('*')->from($this->tableName)->fetchAssoc($this->primaryKey);
	}

	public function findBy(array $by, ?string $orderBy = null): mixed {
		$query = $this->db->select('*')->from($this->tableName)->where($by);

		if ($orderBy) {
			$query->orderBy($orderBy);
		}

		return $query;
	}

	public function rowExist(array $by): bool {
		return (bool) $this->db->select(1)->from($this->tableName)->where($by)->fetchSingle();
	}

	public function findOneBy(array $by, ?string $orderBy = null): mixed {

		return $this->findBy($by,$orderBy)->fetch();
	}

	public function findOneValueBy(string $value, array $by): mixed {
		return $this->db->select($value)->from($this->tableName)->where($by)->fetchSingle();
	}

	public function getIDBy(array $by): mixed {
		return $this->db->select($this->primaryKey)->from($this->tableName)->where($by)->fetchSingle();
	}

	public function getIDsBy(array $by): array {
		return $this->db->select($this->primaryKey)->from($this->tableName)->where($by)->fetchPairs($this->primaryKey, $this->primaryKey);
	}

	public function findAllBy(array $by = [], ?int $limit = null, ?int $offset = null, ?string $orderBy = null): array {

		$query = $this->db->select('*')->from($this->tableName);
		if ($by) {
			$query->where($by);
		}
		if ($limit) {
			$query->limit($limit);
		}
		if ($offset) {
			$query->offset($offset);
			$query->limit($limit);
		}
		if ($orderBy) {
			$query->orderBy($orderBy);
		}


		return $query->fetchAssoc($this->primaryKey);
	}

	public function find(mixed $id): mixed {
		return $this->db->select('*')->from($this->tableName)->where([$this->primaryKey => $id])->fetch();
	}

	public function count(array $where = []): int {
		if ($where) {
			return (int)$this->db->select('COUNT(*)')->from($this->tableName)->where($where)->fetchSingle();
		}
		return (int)$this->db->select('COUNT(*)')->from($this->tableName)->fetchSingle();
	}

	public function sum(string $field, array $where = []): mixed {
		if ($where) {
			return $this->db->select('SUM(' . $field . ')')->from($this->tableName)->where($where)->fetchSingle();
		}
		return $this->db->select('SUM(' . $field . ')')->from($this->tableName)->fetchSingle();
	}

	public function update(mixed $data): mixed {
		$q = $this->getUpdateQuery($data);
		if($q){
			return $this->doUpdate($q);
		}
		return 0;
	}

	private function getUpdateQuery(mixed $data): mixed {

		if ($data instanceof IEntity) {
			$data = $data->getEntityData();
			//unset defaults if exist
		}


		if (isset($data[$this->primaryKey])) {
			$id = $data[$this->primaryKey];
			unset($data[$this->primaryKey]);

			if(array_key_exists('created_dt', $data))
				unset($data['createdDt']);

			return $this->db->update($this->tableName, $data)->where($this->primaryKey . ' = %i', $id);
		}
		return 0;
	}



	public function insert(mixed $data): mixed {
		$dataArray = null;
		if ($data instanceof IEntity) {
			$dataArray = $data->getEntityData();
		}elseif(is_array($data)){
			$dataArray = $data;
		}

		if(isset($dataArray)){
			$this->db->insert($this->tableName, $dataArray)->execute();
			$lastInsertId = $this->db->getInsertId();

			if ($data instanceof IEntity) {
				$data->setId($lastInsertId);
			}
			return $lastInsertId;
		}
		return false;

	}

	public function delete(mixed $id): mixed {
		$this->db->delete($this->tableName)->where($this->primaryKey . ' = %i', $id)->execute();
		return $this->db->getAffectedRows();
	}

	public function deleteBy(array $by): mixed {
		$this->db->delete($this->tableName)->where($by)->execute();
		return $this->db->getAffectedRows();
	}

	public function assocArray(string $key, array $array): array {
		$newArray = [];
		if (!empty($array)) {
			foreach ($array as $value) {
				$newArray[$value->$key][] = $value;
			}
		}
		return $newArray;
	}

	public function getTableFields(): Result {
		return $this->db->query('SHOW FIELDS FROM ' . $this->tableName . '');
	}

	public function save(IEntity $entity, bool $withTranslation = false): IEntity {
		if ($entity->getId()) {
			$this->update($entity, $withTranslation);
		} else {
			$id = $this->insert($entity->getEntityData());
			$entity->setId($id);
		}
		return $entity;
	}



	public function getLookupOptions(int $pid, mixed $query = false): array {

		$rs =  $this->db->select('*')->from('lookup')
				->where('pid=%i',$pid);

		if($query){
			$rs->where('item LIKE %like~',$query);
		}

		return $rs->fetchPairs('id','item');
	}

	public function getLookupList(int $pid): array {
		return $this->db->select('*')->from('lookup')
				->where('pid=%i',$pid)->fetchAssoc('id');
	}

	public function getLookupItem(int $lookupId): mixed {
		return $this->db->select('item')->from('lookup')->where('id=%i',$lookupId)->fetchSingle();
	}


	private function doUpdate(Fluent $q): int {
		$q->execute();
		return $this->db->getAffectedRows();
	}
}
