<?php
namespace Dibi\Drivers;

use Dibi;
use Tracy\Debugger;

class MySqlideadlockDriver extends MySqliDriver implements Dibi\Driver, Dibi\ResultDriver
{
	//use Dibi\Strict;
	public function query(string $sql): ?\Dibi\ResultDriver
	{
		$tries = 3;
		while($tries--){
			try{
				return parent::query($sql);
			}catch (Dibi\DriverException $e){
				if($e->getCode()==1213 and $tries > 0){
					$logE = new Dibi\DriverException("Attempt no. ".(3-$tries).": ".$e->getMessage(), $e->getCode(), $e->getSql());
					Debugger::log($logE, 'CatchedDeadlock');
					continue;
				}
				throw  $e;
			}
		}
	}
}