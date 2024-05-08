<?php
namespace Freightera\AuthClient\Client;

use Freightera\AuthClient\Exceptions\AuthClientException;
use InvalidArgumentException;

class AuthClient
{
	/** @var CurlClient */
	private $httpClient;

	/** @var string */
	private $baseUrl;

	/** @var string */
	private $clientId;

	/** @var string */
	private $clientSecret;

	/** @var string */
	private $userAgent;

	public function __construct(
		string $baseUrl,
		string $clientId,
		string $clientSecret
	) {
		if (empty($baseUrl)) {
			throw new InvalidArgumentException('Base URL is missing on Freightera Auth Client.');
		}
		if (empty($clientId)) {
			throw new InvalidArgumentException('Client ID is missing on Freightera Auth Client.');
		}
		if (empty($clientSecret)) {
			throw new InvalidArgumentException('Client Secret is missing on Freightera Auth Client.');
		}
		$this->baseUrl = $baseUrl;
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->httpClient = new CurlClient();
	}

	public function withTimeout(int $timeout): self
	{
		$this->httpClient->withTimeout($timeout);
		return $this;
	}

	public function withUserAgent(string $userAgent): self
	{
		$this->userAgent = $userAgent;
		return $this;
	}

	private function createBaseOAuthRequest(string $grantType, string $scope): array
	{
		return [
			'grant_type' => $grantType,
			'client_id' => $this->clientId,
			'client_secret' => $this->clientSecret,
			'scope' => $scope,
		];
	}

	public function getAccessToken(string $userType, string $email, string $password, string $scope = ''): AuthClientResponse
	{
		$body = array_merge(
			$this->createBaseOAuthRequest('password', $scope),
			[
				'username' => $email,
				'password' => $password,
				'user_type' => $userType,
			]
		);
		$options = [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $body,
			CURLOPT_HTTPHEADER => [
				'Accept: application/json',
				'User-Agent: ' . $this->userAgent,
			]
		];
		try {
			return $this->httpClient->request($this->baseUrl . '/oauth/token', $options);
		} catch (AuthClientException $authClientException) {
			if ($authClientException->getCode() === 401) {
				$originalResponse = $authClientException->getResponse();
				throw new AuthClientException(new AuthClientResponse(
					$originalResponse->getCurlCode(),
					422,
					$originalResponse->getRequestedURL()
				), "Incorrect email address or password");
			} else {
				throw $authClientException;
			}
		}
	}

	public function getRefreshToken(string $refreshToken, string $scope = ''): AuthClientResponse
	{
		$body = array_merge(
			$this->createBaseOAuthRequest('refresh_token', $scope),
			[
				'refresh_token' => $refreshToken,
			]
		);
		$options = [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $body,
			CURLOPT_HTTPHEADER => [
				'Accept: application/json',
				'User-Agent: ' . $this->userAgent,
			]
		];
		return $this->httpClient->request($this->baseUrl . '/oauth/token', $options);
	}

	public function getAccessTokenWithClientCredentials(string $scope = ''): AuthClientResponse
	{
		$body = $this->createBaseOAuthRequest('client_credentials', $scope);
		$options = [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $body,
			CURLOPT_HTTPHEADER => [
				'Accept: application/json',
				'User-Agent: ' . $this->userAgent,
			]
		];
		return $this->httpClient->request($this->baseUrl . '/oauth/token', $options);
	}
}
