<?php

namespace Freightera\AuthClient\Verifier;

class JWTAuth
{
	public const SHIPPER_USER_TYPE = 'shipper';
	public const CARRIER_USER_TYPE = 'carrier';
	public const ADMIN_USER_TYPE = 'admin';

	/** @var string */
	private $rawToken;

	/** @var integer */
	private $userId;

	/** @var string */
	private $userType;

	/**
	 * @param $rawToken
	 * @param $userId
	 * @param $userType
	 */
	public function __construct(string $rawToken, ?string $userId, ?string $userType)
	{
		$this->rawToken = $rawToken;
		$this->userId = $userId;
		$this->userType = $userType;
	}

	/**
	 * @return string
	 */
	public function getRawToken(): string
	{
		return $this->rawToken;
	}

	/**
	 * @return int
	 */
	public function getUserId(): ?int
	{
		return $this->userId;
	}

	/**
	 * @return string
	 */
	public function getUserType(): ?string
	{
		return $this->userType;
	}

	public function isShipper(): bool
	{
		return $this->userType === self::SHIPPER_USER_TYPE;
	}

	public function isCarrier(): bool
	{
		return $this->userType === self::CARRIER_USER_TYPE;
	}

	public function isAdmin(): bool
	{
		return $this->userType === self::ADMIN_USER_TYPE;
	}
}
