<?php

namespace Freightera\AuthClient\Client;

class AuthClientResponse
{
	/** @var int */
	private $statusCode;
	/** @var string */
	private $body;
	/** @var int */
	private $curlCode;
	/** @var string */
	private $requestedURL;

	public function __construct(int $curlCode, int $statusCode, string $requestedURL)
	{
		$this->curlCode = $curlCode;
		$this->statusCode = $statusCode;
		$this->requestedURL = $requestedURL;
	}

	public function withBody(string $body): self
	{
		$this->body = $body;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getStatusCode(): int
	{
		return $this->statusCode;
	}

	/**
	 * @return string
	 */
	public function getBody(): ?string
	{
		return $this->body;
	}

	/**
	 * @return int
	 */
	public function getCurlCode(): int
	{
		return $this->curlCode;
	}

	public function isOK(): int
	{
		return $this->curlCode === 0 && $this->statusCode >= 200 && $this->statusCode < 300;
	}

	public function decodeBody(): ?array
	{
		$responseJson = json_decode($this->body, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			return null;
		}
		return $responseJson;
	}

	/**
	 * @return string
	 */
	public function getRequestedURL(): string
	{
		return $this->requestedURL;
	}

	public function toArray(): array
	{
		return [
			'curlCode' => $this->curlCode,
			'statusCode' => $this->statusCode,
			'requestedURL' => $this->requestedURL,
			'body' => $this->body,
		];
	}
}
