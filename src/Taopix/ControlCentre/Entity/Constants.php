<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\ConstantsRepository;

#[ORM\Entity(repositoryClass: ConstantsRepository::class), ORM\Table(name: "constants", schema: "controlcentre")]
class Constants
{
	#[ORM\Id, ORM\GeneratedValue, ORM\Column(name: "id")]
	private ?int $id = null;

	#[ORM\Column(name: "datecreated", nullable: false, options: ["default" => "0000-00-00 00:00:00"])]
	private ?DateTime $dateCreated = null;

	#[ORM\Column(name: "defaultcurrencycode", length: 50, nullable: false, options: ["default" => ""])]
	private string $defaultCurrency = '';

	#[ORM\Column(name: "defaultlanguagecode", length: 50, nullable: false, options: ["default" => ""])]
	private string $defaultLanguageCode = '';

	#[ORM\Column(name: "defaultcreditlimit", type: Types::DECIMAL, precision: 10, scale: 2, nullable: false, options: ["default" => "0.00"])]
	private string $defaultCreditLimit = '0.00';

	#[ORM\Column(name: "maxloginattempts", type: Types::SMALLINT, nullable: false, options: ["default" => 10])]
	private int $maxLoginAttempts = 10;

	#[ORM\Column(name: "accountlockouttime", nullable: false, options: ["default" => 15])]
	private int $accountLockoutTime = 15;

	#[ORM\Column(name: "maxiploginattempts", type: Types::SMALLINT, nullable: false, options: ["default" => 15, "unsigned" => true])]
	private int $maxIpLoginAttempts = 15;

	#[ORM\Column(name: "maxiploginattemptsminutes", type: Types::SMALLINT, nullable: false, options: ["default" => 15, "unsigned" => true])]
	private int $maxIpLoginAttemptsMinutes = 15;

	#[ORM\Column(name: "minpasswordscore", type: Types::SMALLINT, nullable: false, options: ["default" => 0, "unsigned" => true])]
	private int $minPasswordScore = 0;

	#[ORM\Column(name: "customerupdateauthrequired", type: Types::SMALLINT, nullable: false, options: ["default" => 0, "unsigned" => true])]
	private int $customerUpdateAuthRequired = 0;

	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @param int|null $id
	 * @return Constants
	 */
	public function setId(?int $id): Constants
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getDateCreated(): ?DateTime
	{
		return $this->dateCreated;
	}

	/**
	 * @param DateTime|null $dateCreated
	 * @return Constants
	 */
	public function setDateCreated(?DateTime $dateCreated): Constants
	{
		$this->dateCreated = $dateCreated;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDefaultCurrency(): string
	{
		return $this->defaultCurrency;
	}

	/**
	 * @param string $defaultCurrency
	 * @return Constants
	 */
	public function setDefaultCurrency(string $defaultCurrency): Constants
	{
		$this->defaultCurrency = $defaultCurrency;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDefaultLanguageCode(): string
	{
		return $this->defaultLanguageCode;
	}

	/**
	 * @param string $defaultLanguageCode
	 * @return Constants
	 */
	public function setDefaultLanguageCode(string $defaultLanguageCode): Constants
	{
		$this->defaultLanguageCode = $defaultLanguageCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDefaultCreditLimit(): string
	{
		return $this->defaultCreditLimit;
	}

	/**
	 * @param string $defaultCreditLimit
	 * @return Constants
	 */
	public function setDefaultCreditLimit(string $defaultCreditLimit): Constants
	{
		$this->defaultCreditLimit = $defaultCreditLimit;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getMaxLoginAttempts(): int
	{
		return $this->maxLoginAttempts;
	}

	/**
	 * @param int $maxLoginAttempts
	 * @return Constants
	 */
	public function setMaxLoginAttempts(int $maxLoginAttempts): Constants
	{
		$this->maxLoginAttempts = $maxLoginAttempts;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getAccountLockoutTime(): int
	{
		return $this->accountLockoutTime;
	}

	/**
	 * @param int $accountLockoutTime
	 * @return Constants
	 */
	public function setAccountLockoutTime(int $accountLockoutTime): Constants
	{
		$this->accountLockoutTime = $accountLockoutTime;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getMaxIpLoginAttempts(): int
	{
		return $this->maxIpLoginAttempts;
	}

	/**
	 * @param int $maxIpLoginAttempts
	 * @return Constants
	 */
	public function setMaxIpLoginAttempts(int $maxIpLoginAttempts): Constants
	{
		$this->maxIpLoginAttempts = $maxIpLoginAttempts;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getMaxIpLoginAttemptsMinutes(): int
	{
		return $this->maxIpLoginAttemptsMinutes;
	}

	/**
	 * @param int $maxIpLoginAttemptsMinutes
	 * @return Constants
	 */
	public function setMaxIpLoginAttemptsMinutes(int $maxIpLoginAttemptsMinutes): Constants
	{
		$this->maxIpLoginAttemptsMinutes = $maxIpLoginAttemptsMinutes;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getMinPasswordScore(): int
	{
		return $this->minPasswordScore;
	}

	/**
	 * @param int $minPasswordScore
	 * @return Constants
	 */
	public function setMinPasswordScore(int $minPasswordScore): Constants
	{
		$this->minPasswordScore = $minPasswordScore;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCustomerUpdateAuthRequired(): int
	{
		return $this->customerUpdateAuthRequired;
	}

	/**
	 * @param int $customerUpdateAuthRequired
	 * @return Constants
	 */
	public function setCustomerUpdateAuthRequired(int $customerUpdateAuthRequired): Constants
	{
		$this->customerUpdateAuthRequired = $customerUpdateAuthRequired;
		return $this;
	}

}