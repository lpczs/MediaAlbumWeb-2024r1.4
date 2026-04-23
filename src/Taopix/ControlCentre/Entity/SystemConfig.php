<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\SystemConfigRepository;

#[ORM\Entity(repositoryClass: SystemConfigRepository::class), ORM\Table(name: "systemconfig", schema: "controlcentre")]
class SystemConfig
{
	#[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
	
	#[ORM\Column(name: "datecreated", options: ["default" => "CURRENT_TIMESTAMP"])]
	private ?DateTime $dateCreated = null;

	#[ORM\Column(name: "ownercode", length: 50, nullable: false)]
	private string $ownerCode = '';

	#[ORM\Column(name: "ownercode2", length: 50, nullable: false)]
	private string $ownerCode2 = '';

	#[ORM\Column(name: "systemkey", length: 100, nullable: false)]
	private string $systemKey = '';

	#[ORM\Column(name: "systemcertificate", length: 16384, nullable: false)]
	private string $systemCertificate = '';

	#[ORM\Column(name: "tenantid", nullable: false)]
	private int $tenantId = 0;

	#[ORM\Column(name: "tenantkey", length: 10, nullable: false)]
	private string $tenantKey = '';

	#[ORM\Column(name: "tenantsecret", length: 32, nullable: false)]
	private string $tenantSecret = '';

	#[ORM\Column(name: "key", length: 50, nullable: false)]
	private string $key = '';

	#[ORM\Column(name: "secret", length: 100, nullable: false)]
	private string $secret = '';

	#[ORM\Column(name: "config", nullable: false)]
	private int $config = 0;

	#[ORM\Column(name: "webversiondate", nullable: false)]
	private ?DateTime $webVersionDate = null;

	#[ORM\Column(name: "webversionnumber", length: 20, nullable: false)]
	private string $webversionnumber = '';

	#[ORM\Column(name: "webversionstring", length: 20, nullable: false)]
	private string $webversionstring = '';

	#[ORM\Column(name: "lastinstallscriptnumber", length: 4, nullable: false)]
	private string $lastInstallScriptNumber = '';

	#[ORM\Column(name: "cronlastruntime", nullable: true)]
	private ?DateTime $cronLastRunTime = null;

	#[ORM\Column(name: "cronactive", nullable: false)]
	private int $cronActive = 0;

	#[ORM\Column(name: "supportedlocales", length: 200, nullable: false)]
	private string $supportedLocales = '';

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return self
     */
    public function setId(?int $id): self
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
	 * @return self
	 */
	public function setDateCreated(?DateTime $dateCreated): self
	{
		$this->dateCreated = $dateCreated;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOwnerCode(): string
	{
		return $this->ownerCode;
	}

	/**
	 * @param string $ownerCode
	 * @return self
	 */
	public function setOwnerCode(string $ownerCode): self
	{
		$this->ownerCode = $ownerCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOwnerCode2(): string
	{
		return $this->ownerCode2;
	}

	/**
	 * @param string $ownerCode2
	 * @return self
	 */
	public function setOwnerCode2(string $ownerCode2): self
	{
		$this->ownerCode2 = $ownerCode2;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSystemKey(): string
	{
		return $this->systemKey;
	}

	/**
	 * @param string $systemKey
	 * @return self
	 */
	public function setSystemKey(string $systemKey): self
	{
		$this->systemKey = $systemKey;
		return $this;
	}	

	/**
	 * @return string
	 */
	public function getSystemCertificate(): string
	{
		return $this->systemCertificate;
	}

	/**
	 * @param string $systemKey
	 * @return self
	 */
	public function setSystemCertificate(string $systemCertificate): self
	{
		$this->systemCertificate = $systemCertificate;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getTenantId(): int
	{
		return $this->tenantId;
	}

	/**
	 * @param int $tenantId
	 * @return self
	 */
	public function setTenantId(int $tenantId): self
	{
		$this->tenantId = $tenantId;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getConfig(): int
	{
		return $this->config;
	}

	/**
	 * @param int $config
	 * @return self
	 */
	public function setConfig(int $config): self
	{
		$this->config = $config;
		return $this;
	}
}