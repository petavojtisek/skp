<?php
namespace GoogleApiClient;


class GoogleApiClient
{


	public function getUserProfileInfo($url, string $access_token) {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '. $access_token]);
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code != 200) {
            throw new Exception('Error : Failed to get user information');
        }

		return $data;
	}
}

?>