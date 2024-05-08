<?php

namespace Freightera\AuthClient\Exceptions;

use Throwable;

class AuthenticationException extends \RuntimeException
{
	public function __construct($message = "", $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

}