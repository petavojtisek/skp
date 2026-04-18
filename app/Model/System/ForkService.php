<?php

namespace App\Model\Service;


use Tracy\Debugger;
use Tracy\ILogger;

class ForkService
{
	/** @var string */
	protected string $route;

    protected string  $file;

	/** @var int */
	protected int $sleep;

	/** @var array */
	protected array $params = [];

	/** @var mixed|string */
	private string $php_bin = 'php';

	/** @var bool */
	protected bool $debug = false;

	/**
	 * ForkService constructor.
	 * @param $cfg
	 * @throws \Exception
	 */
	public function __construct($cfg)
	{
		if (!is_array($cfg)) {
			$cfg = array();
		}

		if (array_key_exists('php_bin', $cfg)) {
			$this->php_bin = trim($cfg['php_bin']);
		}
	}

	/**
	 * @param string $route
	 * @return $this
	 */
	public function setRoute(string $route)
	{
		$this->route = $route;
		return $this;
	}

    public function setFile(string $file)
    {
        $this->file = $file;
        return $this;
    }

	/**
	 * @param int $sleepTime in seconds
	 * @return $this
	 */
	public function setSleep(int $sleepTime)
	{
		$this->sleep = $sleepTime;
		return $this;
	}

	/**
	 * @param array $params
	 * @return $this
	 */
	public function setParams($params)
	{
		$this->params = $params;
		return $this;
	}

	/**
	 * fork precess on background
	 */
	public function run()
	{
		$path = DIR_WWW . DS . 'index.php ' . $this->route . $this->getParams() . $this->getSleep();
		$clidebug = ($this->debug ? ' -dxdebug.remote_autostart=On ' : '');

		if (PHP_OS === "WINNT") {
			pclose(popen('start "" ' . $this->php_bin . $clidebug .' -f ' . $path, 'r'));
		} else {
			exec($this->php_bin . $clidebug .' -f ' . $path . ' >/dev/null 2>&1 &');
		}
		$this->params = array();
		$this->sleep = null;
		$this->route = null;
	}

    public function runBin()
    {
        $path = PROJECT_ROOT_DIR . DS . 'bin' .DS . $this->file.".php" . $this->getParams() . $this->getSleep();
        $clidebug = ($this->debug ? ' -dxdebug.remote_autostart=On ' : '');

        if (PHP_OS === "WINNT") {
            pclose(popen('start "" ' . $this->php_bin . $clidebug .' -f ' . $path, 'r'));
        } else {
            exec($this->php_bin . $clidebug .' -f ' . $path . ' >/dev/null 2>&1 &');
        }
        $this->params = array();
        $this->sleep = null;
        $this->route = null;
    }

	/**
	 * @param bool $includingParams
	 * @return bool
	 */
	public function runSingleInstance($includingParams = false)
	{
		$params = [];

		if($includingParams)
			$params = $this->params;

		try
		{
			$procInfo = $this->getAppProcessInfo($this->route, $params);
		}
		catch (\Exception $exception)
		{
			Debugger::log($exception, ILogger::EXCEPTION);
		}

		if (isset($procInfo) && $procInfo === false)
		{
			$this->run();
			return true;
		}

		return false;
	}

	/**
	 * get sleep if exist
	 * @return string
	 */
	private function getSleep() {
		if ($this->sleep) {
			return ' -cronSleepParam=' . $this->sleep . ' ';
		}
		return '';
	}

	/**
	 * @return string
	 */
	private function getParams() {
		$params = '';
		foreach ($this->params as $index => $param) {
			$params .= ' -' . $index . '=' . $param;
		}
		return $params;
	}

	/**
	 * Kill the process
	 * @param int $pid
	 * @return bool
	 */
	public function kill($pid)
	{
		exec('kill -9 '. (int) $pid, $o, $r);
		return $r == 0;
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function getProcesses()
	{

		if(defined("RONDO_DEV") and RONDO_DEV)
		{
			$return = 0;
			$output = [];

			// Windows OS
			if (PHP_OS === 'WINNT')
			{
				$output_wmic = [];
				$output_powershell = [];
				$output_tasklist = [];

				$processes = [];

				exec('wmic process GET ProcessId,CommandLine', $output_wmic);
				exec('powershell "Get-Process | Select-Object id, starttime"', $output_powershell);
				exec('tasklist /v /fo csv', $output_tasklist);

				for ($i = 3; $i < count($output_wmic); $i++)
				{
					if (preg_match('/^(.+?)\s+(\d+)$/', $output_wmic[$i], $matches) === 1)
					{
						$cmd = trim($matches[1]);
						$pid = $matches[2];

						if (!empty($cmd))
						{
							if (!array_key_exists($pid, $processes))
								$processes[$pid] = [];

							$processes[$pid]['command'] = $cmd;
						}
					}
				}

				for($i = 3; $i < count($output_powershell); $i++)
				{
					if(preg_match('/^\s+(\d+)\s(.+)$/', $output_powershell[$i], $matches) === 1)
					{
						$pid = $matches[1];
						$etime = time() - strtotime(str_replace('. ', '.', $matches[2]));
						$etime_str = sprintf('%s-%02s:%02s:%02s', floor($etime / 86400), floor($etime / 3600) % 24, floor($etime / 60) % 60, $etime % 60);

						if (!array_key_exists($pid, $processes))
							$processes[$pid] = [];

						$processes[$pid]['etime'] = $etime_str;
					}
				}

				for($i = 3; $i < count($output_tasklist); $i++)
				{
					$line = str_getcsv($output_tasklist[$i]);

					$pid = $line[1];
					$user = substr(strstr($line[6], '\\'), 1);

					if (!array_key_exists($pid, $processes))
						$processes[$pid] = [];

					$processes[$pid]['user'] = $user;
				}

				$output = [];

				foreach ($processes as $pid => $process)
				{
					if (isset($process['etime'], $process['user'], $process['command']))
					{
						$output[] = implode('  ', [$pid, $process['etime'], $process['user'], $process['command']]);
					}
				}
			}
		}
		else
		{
			exec("ps -o pid,etime,user,command -ax", $output, $return);
		}

		if ($return > 0) {
			throw new \Exception('Unable to check process list');
		}


		array_shift($output);
		$responses = array();
		foreach ($output as $line) {
			$line = preg_split('#\s+#', trim($line), 4);
			if(count($line) == 4){
				$responses[] = $line;
			}
		}
		return $responses;
	}

	/**
	 *
	 * @param $name
	 * @return array|bool
	 * array(
	 *     array(
	 *         'running' => true,
	 *         'pid' => int,
	 *         'time' => int - seconds,
	 *         'user' => 'user'
	 *	   )
	 * )
	 */
	public function getProcessInfo($name)
	{
		$name = preg_quote($name, '#');
		$processes = $this->getProcesses();
		$found = [];
		foreach ($processes as $process) {
			$cmd = $process[3];
			if (preg_match('#' . $name . '#i', $cmd. ' ')) {
				$found[] = array(
					'pid' => $process[0],
					'user' => $process[2],
					'time' => $this->parseTime($process[1]),
					'cmd' => $cmd,
				);
			}
		}
		return (empty($found)) ? false : $found;
	}

    /**
     * @param $filterRoute
     * @param array $filterParams
     * @return bool
     * @throws \Exception
     */
	public function getAppProcessInfo($filterRoute, $filterParams = [])
	{
		if(preg_match("#([\w\:]+)#", $filterRoute, $m)){
			$filterRoute = preg_quote($m[1], '#');
		}else{
			$filterRoute = "(.*)";
		}

		$found = [];
		$processes = $this->getProcesses();
		foreach ($processes as $process) {
			$cmd = $process[3];
			$route = $this->parseRoute($cmd);
			if ($route and preg_match('#' . $filterRoute . '#i', $route) and strpos($cmd, DIR_WWW) !== false) {
				$params = $this->parseParams($cmd);
				$filterOk = true;
				foreach($filterParams as $key => $val){
					if(!isset($params[$key]) or $val != $params[$key]){
						$filterOk = false;
						break;
					}
				}

				if(!$filterOk){
					continue;
				}

				$found[] = array(
					'pid' => $process[0],
					'time' => $this->parseTime($process[1]),
					'user' => $process[2],
					'cmd' => $cmd,
					'route' => $route,
					'params' => $params,
				);
			}
		}
		return (empty($found)) ? false : $found;
	}


	/**
	 * time prepare
	 * @param string $time
	 * @return int
	 * @throws \Exception
	 */
	public function parseTime($time)
	{
		# 31-17:51:39
		if (preg_match('#^(?:(\d+)-)?(?:(\d\d):)?(\d\d):(\d\d)$#', trim($time), $matches)) {
			$matches[1] = empty($matches[1]) ? 0 : $matches[1];
			$matches[2] = empty($matches[2]) ? 0 : $matches[2];
			return $matches[1] * 86400 // days
			+ $matches[2] * 3600 // hours
			+ $matches[3] * 60 // minutes
			+ $matches[4]; // seconds
		}

		throw new \Exception('Unable to detect time in process list: '. $time);
	}

	public function parseRoute($cmd){
		$route = null;
		if(preg_match("#\.php\s([\w\:]+)#", $cmd, $m)){
			$route = $m[1];
		}
		return $route;
	}

	public function parseParams($cmd){
		$params = [];
		preg_match_all("#-(\w+)=([^\s]+)#", $cmd, $m, PREG_SET_ORDER);
		if(!empty($m)){
			foreach($m as $param){
				list(,$key, $val) = $param;
				$params[$key] = $val;
			}
		}
		return $params;

	}

	/**
	 * @return bool
	 */
	public function getDebug() {
		return $this->debug;
	}

	/**
	 * @param bool $debug
	 */
	public function setDebug($debug) {
		$this->debug = $debug;
		return $this;
	}
}
