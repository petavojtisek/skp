<?php
namespace App\Model\System;
class Environment{

	const ENV_DEV = 'DEV';
	const ENV_TEST = 'TEST';
	const ENV_PRODUCTION = 'PRODUCTION';

	private $environmentName;

	public function __construct($environmentName = null)
	{
		if($environmentName === null){
			if(defined('RONDO_DEV') and RONDO_DEV){
				$this->environmentName = static::ENV_DEV;
			}elseif (defined('RONDO_TEST') and RONDO_TEST){
				$this->environmentName = static::ENV_TEST;
			}else{
				$this->environmentName = static::ENV_PRODUCTION;
			}
		}else{
			if(in_array($environmentName, $this->getAvailableEnvironments())){
				$this->environmentName = $environmentName;
			}else{
				throw new \Exception('Unable to detect Environment!');
			}
		}
	}

	public function getAvailableEnvironments(){
		return [static::ENV_DEV, static::ENV_TEST, static::ENV_PRODUCTION];
	}

	public function isDev(){
		return ($this->environmentName === static::ENV_DEV);
	}

	public function isTest(){
		return ($this->environmentName === static::ENV_TEST);
	}

	public function isProduction(){
		return ($this->environmentName === static::ENV_PRODUCTION);
	}

	public function getServerNumber()
	{
		if($this->isProduction() and preg_match('#www(\d*)\.rondo(?:go)?\.\w{2,3}#', filter_input(INPUT_SERVER, 'HTTP_HOST'), $match))
		{
			return ($match[1] === '' ? 1 : intval($match[1]));
		}

		return false;
	}

	public function getTestNumber(){
		if($this->isTest() and preg_match('#/rtest(\d+)/#i',__DIR__, $match)){
			return (int)$match[1];
		}
		return false;
	}

	public function getEnvironmentName(){
		return $this->environmentName;
	}



}