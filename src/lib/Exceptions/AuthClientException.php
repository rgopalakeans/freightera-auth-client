<?php

namespace Freightera\AuthClient\Exceptions;

use Freightera\AuthClient\Client\AuthClientResponse;

class AuthClientException extends \RuntimeException
{
	/** @var AuthClientResponse */
	private $response;

	public function __construct(AuthClientResponse $response, string $message = '')
	{
		$this->response = $response;
		parent::__construct($message, $response->getStatusCode());
	}

	/**
	 * @return AuthClientResponse
	 */
	public function getResponse(): AuthClientResponse
	{
		return $this->response;
	}


}