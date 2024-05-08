<?php

namespace Freightera\AuthClient\Client;

use Freightera\AuthClient\Exceptions\AuthClientException;

class CurlClient
{
	/** @var int */
	private $timeout = 60;
	/** @var int */
	private $userAgent = 'Freightera Auth Client';

	public function withTimeout(int $timeout): self
	{
		$this->timeout = $timeout;
		return $this;
	}

	/**
	 * @param string $url
	 * @param array $curlOptions
	 * @return string
	 */
	public function request(
		string $url,
		array  $curlOptions = []
	): AuthClientResponse {
		$curl = curl_init();
		try {
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
			curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
			curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			if (!empty($curlOptions)) {
				foreach ($curlOptions as $option => $value) {
					curl_setopt($curl, $option, $value);
				}
			}

			$response = curl_exec($curl);

			$lastResponse = new AuthClientResponse(
				curl_errno($curl),
				curl_getinfo($curl, CURLINFO_HTTP_CODE),
				$url
			);

            //error_log(print_r($url, true));
            //error_log(print_r($curlOptions, true));
            //error_log(print_r($lastResponse, true));
            //error_log(print_r($response, true));

			if ($response !== false) {
				$lastResponse->withBody($response);
			}

			if (! $lastResponse->isOK()) {
				throw new AuthClientException($lastResponse, curl_error($curl));
			}
		} finally {
			curl_close($curl);
		}
		return $lastResponse;
	}
}
