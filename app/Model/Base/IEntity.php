<?php
/**
 * Created by PhpStorm.
 * User: PetrV
 * Date: 3.2.2019
 * Time: 10:16
 */

namespace App\Model\Base;


interface IEntity
{

	/**
	 * get Id
	 * @return int
	 */
	public function getId();

	/**
	 * set new id
	 * @param int $id
	 */
	public function setId($id);

	/**
	 * Get core data
	 * @return array
	 */
	public function getEntityData();

	/**
	 * @param array $data
	 * @return mixed
	 */
	public function fillEntity($data = []);

}