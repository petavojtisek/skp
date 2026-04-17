<?php


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


