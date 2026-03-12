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


