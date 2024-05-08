<?php
namespace Freightera\AuthClient\Verifier;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Freightera\AuthClient\Exceptions\AuthenticationException;
use InvalidArgumentException;
use Throwable;

class JWTVerifier
{
	/** @var string */
	private $passportPublicKey;

	public function __construct(string $passportPublicKey)
	{
		if (empty($passportPublicKey)) {
			throw new InvalidArgumentException('Passport public key is missing on Freightera Auth Client.');
		}
		$this->passportPublicKey = implode("\r\n", explode('\n', $passportPublicKey));
	}

	/**
	 * @throws AuthenticationException
	 */
	public function getAuthentication(?string $accessToken): JWTAuth
	{
		if (empty($accessToken)) {
			throw new AuthenticationException('Access token is required.', 401);
		}
		try {
			JWT::$leeway = 600; // 10 minutes
			$decoded = (array) JWT::decode($accessToken, new Key($this->passportPublicKey, 'RS256'));
		} catch (Throwable $e) {
			throw new AuthenticationException(sprintf('Error %s when trying to decode the token %s. Public Key: %s', $e->getMessage(), $accessToken, $this->passportPublicKey), 403, $e);
		}
		$values = explode('-', $decoded['sub']);
		switch ($values[0] ?? null) {
			case 'S':
				$userType = JWTAuth::SHIPPER_USER_TYPE;
				break;
			case 'C':
				$userType = JWTAuth::CARRIER_USER_TYPE;
				break;
			case 'A':
				$userType = JWTAuth::ADMIN_USER_TYPE;
				break;
			default:
				$userType = null;
				break;
		}
		$userId = $values[1] ?? null;
		return new JWTAuth($accessToken, $userId, $userType);
	}
}