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
	 * @return int|string|null
	 */
	public function getId(): mixed;

	/**
	 * set new id
	 * @param int|string $id
	 */
	public function setId(mixed $id): void;

	/**
	 * Get core data
	 * @return array
	 */
	public function getEntityData(): array;

	/**
	 * @param array $data
	 * @return mixed
	 */
	public function fillEntity(array $data = []): mixed;

}