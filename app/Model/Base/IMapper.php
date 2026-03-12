<?php
/**
 * Created by Jan Pabyška.
 * User: janpabyska
 * Date: 14.12.15
 * Time: 14:15
 */

namespace App\Model\Base;

use Dibi\Result;
use Dibi\Row;

interface IMapper
{
	/**
	 * begin transaction
	 */
	public function begin(): void;

	/**
	 * commit transaction
	 */
	public function commit(): void;
}



interface Test
{

	/**
	 * rollback transaction
	 */
	public function rollback(): void;

	/**
	 * return all records from table
	 * @return Row[]|array
	 */
	public function findAll(): array;

	/**
	 * return filtered rows by array
	 * (array('name' => 'David') convert to SQL query WHERE name = 'David')
	 * @param array $by
	 * @return Row|null|mixed
	 */
	public function findBy(array $by): mixed;

	/**
	 * return if record already exist
	 * (array('name' => 'David') convert to SQL query WHERE name = 'David')
	 * @param array $by
	 * @return bool
	 */
	public function rowExist(array $by): bool;

	/**
	 * return single row
	 * @param array $by
	 * @return Row|null|mixed
	 */
	public function findOneBy(array $by): mixed;

	/**
	 * return single row
	 * @param string $value name of col
	 * @param array $by
	 * @return mixed
	 */
	public function findOneValueBy(string $value, array $by): mixed;

	/**
	 * return ID of row
	 * @param array $by
	 * @return int|string|null
	 */
	public function getIDBy(array $by): mixed;

	/**
	 * return IDs of row
	 * @param array $by
	 * @return array
	 */
	public function getIDsBy(array $by): array;

	/**
	 * return all row by condition in array
	 * @param array $by
	 * @param int|null $limit
	 * @param int|null $offset
	 * @param string|null $orderBy
	 * @return Row[]|array
	 */
	public function findAllBy(array $by = [], ?int $limit = null, ?int $offset = null, ?string $orderBy = null): array;

	/**
	 * return single row by primary key
	 * @param int|string $id
	 * @return Row|null|mixed
	 */
	public function find(mixed $id): mixed;

	/**
	 * count rows
	 * @param array $where if is not set count all
	 * @return int
	 */
	public function count(array $where = []): int;

	/**
	 * count summary in rows
	 * @param string $field field name to summary
	 * @param array $where if is not set count all
	 * @return int|float
	 */
	public function sum(string $field, array $where = []): mixed;

	/**
	 * update record
	 * @param array|IEntity $data array have to include primary key
	 * @return int|bool
	 */
	public function update(mixed $data): mixed;

	/**
	 * insert record and return insert ID
	 * @param array|IEntity $data
	 * @return int|string|bool of inserted ID
	 */
	public function insert(mixed $data): mixed;

	/**
	 * remove row by ID
	 * @param int|string $id
	 * @return int|bool
	 */
	public function delete(mixed $id): mixed;

	/**
	 * sort multidimensional array
	 * @param string $key - name of the col
	 * @param IEntity[] $array
	 * @return array
	 */
	public function assocArray(string $key, array $array): array;

	/**
	 * get table fields
	 * @return Result
	 */
	public function getTableFields(): Result;

	/**
	 * @param IEntity $entity
	 * @return IEntity
	 */
	public function save(IEntity $entity): IEntity;
}
