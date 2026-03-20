<?php
namespace App\Model\System;

use Firebase\JWT\JWT;
use Tracy\Debugger;
use Tracy\ILogger;

class EncodeDecode
{
	private $JWTSecret     = 'e6JzmfMq6LyzolodwQezGXE5oBi9DvAy2lgSTpXye0972hkCf2kCri6a2rMr0H2G';
	private $algorithm     = 'HS512';
	private $encryptMethod = 'AES-256-CBC';
	private $secretKey     = 'Es5G0i1JpHWLawRNnNKCeuAE7wfGa65VVh2TGUTHCcqTuiXN11vleAYVAeuaYpVV'; // key for URL data encryption and decryption
	private $secretIv      = 'PtjgwounuEg7oNzrLy9wWjzwtaFKeu0u'; // Initialization Vector data for URL data encryption and decryption
	private $smallKey1     = 3296051;
	private $smallKey2     = 2257766;


	/**
	 * Encode integer ID to HashID
	 * @param int $id
	 * @return string
	 */
	public function encodeSmallHash($id)
	{
		if(extension_loaded('gmp'))
			return gmp_strval(gmp_add(gmp_mul(gmp_init(intval($id), 10), $this->smallKey1), $this->smallKey2), 36);

		trigger_error('GNU Multiple Precision library is NOT loaded.', E_USER_WARNING);

		return base_convert($id * $this->smallKey1 + $this->smallKey2, 10, 36);
	}

	/**
	 * Decode HashID to integer ID
	 * @param string $hash
	 * @return int|FALSE
	 */
	public function decodeSmallHash($hash)
	{
		if(extension_loaded('gmp'))
		{
			$lastError = error_get_last();

			//JZ fix na floaty ktere vyjdou z rethinkDB pokud je v tom noSQL klici retezec slozeny ze samych cisel
			//gmp neprijima float, nicmene my float primo cilene nepodavame, je to spis dusledek
			if (is_float($hash) and round($hash) == $hash)
				$hash = (string)$hash;

			$res = @gmp_div_qr(gmp_sub(gmp_init($hash, 36), $this->smallKey2), $this->smallKey1);

			// Zaloguj chyby vznikle pri pokusu o dekodovani hashe
			if (is_array($err = error_get_last()) and $err !== $lastError)
			{
				$e = new \ErrorException($err['message'] . ' - hash: ' . var_export($hash, true), $err['type'], E_WARNING, $err['file'], $err['line']);

				Debugger::log($e, ILogger::EXCEPTION);

				if ( (defined('RONDO_DEV') and RONDO_DEV === TRUE) or (defined('RONDO_TEST') and RONDO_TEST === TRUE) )
					throw $e;
			}

			return (gmp_intval($res[1]) === 0 ? gmp_intval($res[0]) : false);
		}

		trigger_error('GNU Multiple Precision library is NOT loaded.', E_USER_WARNING);

		$id = base_convert($hash, 36, 10);
		if (fmod($id - $this->smallKey2, $this->smallKey1) == 0)
			return ($id - $this->smallKey2) / $this->smallKey1;

		return false;
	}

	/**
	 * encrypt string into hash
	 * @param array $array - assoc array to encrypt
	 * @return string
	 */
	public function encrypt($array)
	{
		list($key, $iv) = $this->getSecretKeys();
		return rtrim(base64_encode(openssl_encrypt(json_encode($array), $this->encryptMethod, $key, 0, $iv)), '=');
	}

	/**
	 * decrypt string into hash
	 * @param string $string - string to encrypt or decrypt
	 * @return array
	 */
	public function decrypt($string)
	{
		list($key, $iv) = $this->getSecretKeys();
		return json_decode(openssl_decrypt(base64_decode($string), $this->encryptMethod, $key, 0, $iv));
	}

	/**
	 * @param mixed $data
	 *
	 * @return string
	 */
	public function encrypt2($data)
	{
		list($key, $iv) = $this->getSecretKeys();
		return @rtrim(strtr(openssl_encrypt(gzcompress(json_encode($data), 9), $this->encryptMethod, $key, 0, $iv), '+/', '-_'), '=');
	}

	/**
	 * @param string $string
	 *
	 * @return null|bool|int|float|string|array
	 */
	public function decrypt2($string, $assoc = true)
	{
		list($key, $iv) = $this->getSecretKeys();
		return @json_decode(gzuncompress(openssl_decrypt(strtr($string, '-_', '+/'), $this->encryptMethod, $key, 0, $iv)), $assoc);
	}

	/**
	 * generate salt 
	 * @return array - $key, $iv
	 */
	private function getSecretKeys()
	{
		// Key
		$key = base64_decode($this->secretKey);

		// IV - encrypt method AES-256-CBC expects 16 bytes - initialization vector(IV)
		$iv = base64_decode($this->secretIv);

		return array(
			$key,
			$iv
		);
	}

	/**
	 * Create pseudorandom binary data
	 *
	 * @param int $length  length of pseudorandom data [OPTIONAL] - default: 20
	 *
	 * @return string - binary data
	 */
	public function generateRandomData($length = 20)
	{
		if(!is_int($length)) throw new \InvalidArgumentException("Length has to be an integer");
		if($length < 1) throw new \InvalidArgumentException("Length has to be greater than 0");

		$data = "";

		if(function_exists("openssl_random_pseudo_bytes"))
		{
			$data = openssl_random_pseudo_bytes($length);
		}
		else
		{
			$i = 0;
			while ( $i++ < $length ) $data .= chr(mt_rand(0,255));
		}

		return $data;
	}

	/**
	 * Create pseudorandom alphanumeric string
	 *
	 * @param int $length  length of pseudorandom string [OPTIONAL] - default: 20
	 *
	 * @return string 
	 */
	public function generateRandomString($length = 20)
	{
		$alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$base = strlen($alphabet);

		$data = "";
		while (strlen($data) < $length)
		{
			$int = hexdec(bin2hex($this->generateRandomData()));
			while($int > 0)
			{
				$data .= substr($alphabet, fmod($int, $base), 1);
				$int = floor($int / $base);
			}
		}

		return substr($data, 0, $length);
	}

	/**
	 * Create unique binary data
	 *
	 * @param int $length  length of unique data [OPTIONAL] - default: 8
	 *
	 * @return string - binary data
	 */
	public function generateUniqueData($length = 8)
	{
		$data = "";

		while(strlen($data) < $length)
		{
			$data .= chr(mt_rand(1,255)).pack('H*', strrev(uniqid()));
		}

		return substr($data, 0, $length);
	}

	/**
	 * Create unique alphanumeric string
	 * @param int $length  length of unique string [OPTIONAL] - default: 12
	 * @return string
	 */
	public function generateUniqueString($length = 12)
	{
		// $alphabet = '2345789abcdefghjkmnpqrstuvwxyz';
		$alphabet = '1234567890ACDEFHJKLMNPRTUVWXYZ';
		$base = strlen($alphabet);

		$data = "";
		while (strlen($data) < $length)
		{
			$int = hexdec(bin2hex($this->generateUniqueData()));
			while($int > 0)
			{
				$data .= substr($alphabet, fmod($int, $base), 1);
				$int = floor($int / $base);
			}
		}
		return substr($data, 0, $length);
	}

	/**
	 * convert interchangeable characters
	 * @param string $code
	 * @return string
	 */
	public function convertNotAllowedCharacters($code)
	{
		$prefix = '';
		if (preg_match("/-/", $code)) {
			if (preg_match("#^test-#i", $code)) {
				$prefix = 'test-';
				$code = str_replace($prefix, '', $code);
			} elseif (preg_match("#^bulk-#i", $code)) {
				$prefix = 'bulk-';
				$code = str_replace($prefix, '', $code);
			} elseif (preg_match("#^axa-#i", $code)) {
				$prefix = 'axa-';
				$code = str_replace($prefix, '', $code);
			}
		}
		$characters = array(
			'B' => 8,
			'G' => 6,
			'I' => 1,
			'O' => 0,
			'Q' => 0,
			'S' => 5,
		);
		return $prefix . str_ireplace(array_keys($characters), array_values($characters), $code);
	}

	/**
	 * @param array $data
	 * @param string $serverName
	 * @param int $expire - default 1 day
	 * @return string
	 */
	public function encodeUserJWTToken($data, $serverName, $expire = 86400)
	{
		$tokenId    = base64_encode(mcrypt_create_iv(32));
		$issuedAt   = time();
		$notBefore  = $issuedAt + 10;  //Adding 10 seconds

		/*
		 * Create the token as an array
		 */
		$data = [
			'iat'  => $issuedAt,   // Issued at: time when the token was generated
			'jti'  => $tokenId,    // Json Token Id: an unique identifier for the token
			'iss'  => $serverName, // Issuer
			'nbf'  => $notBefore , // Not before
			'exp'  => $expire,     // Expire
			'data' => $data        // Data related to the logged user you can set your required data
		];

		$secretKey = base64_decode($this->JWTSecret);
		/// Here we will transform this array into JWT:
		return JWT::encode(
				$data, //Data to be encoded in the JWT
				$secretKey, // The signing key
				$this->algorithm
			);
	}

	/**
	 * @param string $token
	 * @return array
	 */
	public function decodeUserJWTToken($token)
	{
		try
		{
			$secretKey = base64_decode($this->JWTSecret);
			$decodedDataArray = JWT::decode($token, $secretKey, array($this->algorithm));
			return $decodedDataArray['data'];
		}
		catch (\Exception $e)
		{
			return FALSE;
		}
	}

	/**
	 * Security risky function but... Product manager wants it :-(
	 * Generates Initial Password for user by it's Email Address
	 *
	 * @param string $userEmail
	 * @param int $passwordLength [OPTIONAL] - Default 7
	 *
	 * @return string
	 */
	public function generateInitialPassword($userEmail, $passwordLength = 7)
	{
		$alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
		$base = strlen($alphabet);

		$data = "";

		$int = fmod(crc32($this->encrypt2(mb_strtolower($userEmail))) + 0x100000000, 0x100000000);

		while(strlen($data) < $passwordLength)
		{
			$data .= $alphabet[intval(fmod($int, $base))];
			$int = floor($int / $base);
		}

		return $data;
	}

}