<?php

namespace Ares;


Class Ares
{

public $_postFields;
    //private $ares_url = "http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_std.cgi?ico=";
	private string $ares_url = "http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi?ico=";
	public $_webpage = '';
	public $_status = '';

	private string $_useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1';

	private int $_timeout = 60;
	private int $_maxRedirects = 10;

	private bool $authentication = false;
	private bool $auth_name = false;
	private bool $auth_pass = false;


	private bool $_post = false;
	private bool $_noBody = false;



	public function getData($ico = false): array
	{
		$this->call($ico);
		//$file = file_get_contents('http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi?ico='.$ico);

		//$file = file_get_contents('text.xml');
		/*$fp=fopen('text.xml','a++');
		fwrite($fp,$file);
		fclose($fp);*/


		$xml = simplexml_load_string((string) $this->_webpage);
		//$xml=simplexml_load_string($file);


		if ($xml) {
			$ns = $xml->getDocNamespaces();
			$data = $xml->children($ns['are']);
			$el = $data->children($ns['D'])->VBAS;


			if (strval($el->ICO) == $ico) {
				$a['ico'] = @strval($el->ICO);
				$a['dic'] = @strval($el->DIC);
				$a['company'] = @strval($el->OF);
				$a['street'] = @strval($el->AA->NU) . ' ' . @strval($el->AA->CO);
				$a['city'] = @strval($el->AA->N);
				$a['zip'] = @strval($el->AA->PSC);
				$a['vat'] = @substr($el->PSU,5,1);


				$a['status'] = $a['ico'] != '' ? 1 : 0;
			} else {
				$a['status'] = 0;
			}
		} else {
			$a['status'] = 0;
		}
		return $a;
	}


	private function call($ico = false): void
	{
		$s = curl_init();
		curl_setopt($s, CURLOPT_URL, $this->ares_url . $ico);
		curl_setopt($s, CURLOPT_HTTPHEADER, ['Expect:']);
		curl_setopt($s, CURLOPT_TIMEOUT, $this->_timeout);
		curl_setopt($s, CURLOPT_MAXREDIRS, $this->_maxRedirects);
		curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($s,CURLOPT_FOLLOWLOCATION,$this->_followlocation);
		// curl_setopt($s,CURLOPT_COOKIEJAR,$this->_cookieFileLocation);
		// curl_setopt($s,CURLOPT_COOKIEFILE,$this->_cookieFileLocation);

		if ($this->authentication == 1) {
			curl_setopt($s, CURLOPT_USERPWD, $this->auth_name . ':' . $this->auth_pass);
		}

		if ($this->_post) {
			curl_setopt($s, CURLOPT_POST, true);
			curl_setopt($s, CURLOPT_POSTFIELDS, $this->_postFields);

		}
		if ($this->_noBody) {
			curl_setopt($s, CURLOPT_NOBODY, true);
		}
		curl_setopt($s, CURLOPT_USERAGENT, $this->_useragent);
		//curl_setopt($s,CURLOPT_REFERER,$this->_referer);
		$this->_webpage = curl_exec($s);
		$this->_status = curl_getinfo($s, CURLINFO_HTTP_CODE);


		if ($this->_status == 200) {
			//$this->parse_data();
		}


	}


}
