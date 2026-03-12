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
use App\Model\IEntity;

interface IMapper
{
	/**
	 * begin transaction
	 */
	public function begin();

	/**
	 * commit transaction
	 */
	public function commit();
}



interface Test
{

	/**
	 * rollback transaction
	 */
	public function rollback();

	/**
	 * return all records from table
	 * @return Row[]
	 */
	public function findAll();

	/**
     * return filtered rows by array
     * (array('name' => 'David') convert to SQL query WHERE name = 'David')
     * @return Row
     */
    public function findBy(array $by);

	/**
     * return if record already exist
     * (array('name' => 'David') convert to SQL query WHERE name = 'David')
     * @return bool
     */
    public function rowExist(array $by);

	/**
     * return single row
     * @return Row
     */
    public function findOneBy(array $by);

	/**
     * return single row
     * @param string $value name of col
     * @return mixed
     */
    public function findOneValueBy($value, array $by);

	/**
     * return ID of row
     * @return int
     */
    public function getIDBy(array $by);

	/**
     * return ID of row
     * @return int
     */
    public function getIDsBy(array $by);

	/**
	 * return all row by condition in array
	 * @param array $by
	 * @param int $limit
	 * @param int $offset
	 * @param string $orderBy
	 * @return Row[]
	 */
	public function findAllBy($by = [], $limit = null, $offset = null, $orderBy = null);

	/**
	 * return single row by primary key
	 * @param int $id
	 * @return Row
	 */
	public function find($id);

	/**
	 * count rows
	 * @param array $where if is not set count all
	 * @return int
	 */
	public function count($where = []);

	/**
	 * count summary in rows
	 * @param string $field field name to summary
	 * @param array $where if is not set count all
	 * @return int
	 */
	public function sum($field, $where = []);

	/**
	 * update record
	 * @param array $data array have to include primary key
	 */
	public function update($data);

	/**
	 * insert record and return insert ID
	 * @param array $data
	 * @return int of inserted ID
	 */
	public function insert($data);

	/**
	 * remove row by ID
	 * @param int $id
	 */
	public function delete($id);

	/**
	 * sort multidimensional array
	 * @param string $key - name of the col
	 * @param IEntity[] $array
	 * @return array
	 */
	public function assocArray($key, $array);

	/**
	 * get fable fields
	 * @return Result
	 */
	public function getTableFields();

	/**
     * @return IEntity
     */
    public function save(IEntity $entity);
}

