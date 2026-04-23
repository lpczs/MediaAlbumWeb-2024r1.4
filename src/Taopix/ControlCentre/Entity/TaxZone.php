<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\TaxZoneRepository;
use Taopix\ControlCentre\Traits\Entity\ToArrayTrait;

#[ORM\Entity(repositoryClass: TaxZoneRepository::class), ORM\Table(name: "taxzones", schema: "controlcentre")]
#[ORM\UniqueConstraint(name: "code", columns: ["code"]), ORM\Index(columns: ["code"], name: "code")]
class TaxZone
{
	use ToArrayTrait;

	#[ORM\Id, ORM\GeneratedValue, ORM\Column(name: "id", nullable: false)]
	private ?int $id = null;
	
	#[ORM\Column(name: "datecreated", nullable: false)]
	private ?DateTime $dateCreated = null;
	
	#[ORM\Column(name: "companycode", length: 50, nullable: false)]
	private string $companyCode = '';

	#[ORM\Column(name: "code", length: 100, nullable: false)]
	private string $code = '';

	#[ORM\Column(name: "localCode", length: 50, nullable: false)]
	private string $localCode = '';

	#[ORM\Column(name: "name", length: 50, nullable: false)]
	private string $name = '';

	#[ORM\Column(name: "taxlevel1", length: 20, nullable: false)]
	private string $taxLevel1 = '';

	#[ORM\Column(name: "taxlevel2", length: 20, nullable: false)]
	private string $taxLevel2 = '';

	#[ORM\Column(name: "taxlevel3", length: 20, nullable: false)]
	private string $taxLevel3 = '';

	#[ORM\Column(name: "taxlevel4", length: 20, nullable: false)]
	private string $taxLevel4 = '';

	#[ORM\Column(name: "taxlevel5", length: 20, nullable: false)]
	private string $taxLevel5 = '';

	#[ORM\Column(name: "shippingtaxcode", length: 20, nullable: false)]
	private string $shippingTaxCode = '';

	#[ORM\Column(name: "countrycodes", length: 1024, nullable: false)]
	private string $countryCodes = '';

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
	public function getCompanyCode(): string
	{
		return $this->companyCode;
	}

	/**
	 * @param string $companyCode
	 * @return self
	 */
	public function setCompanyCode(string $companyCode): self
	{
		$this->companyCode = $companyCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCode(): string
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 * @return self
	 */
	public function setCode(string $code): self
	{
		$this->code = $code;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLocalCode(): string
	{
		return $this->localCode;
	}

	/**
	 * @param string $localCode
	 * @return self
	 */
	public function setLocalCode(string $localCode): self
	{
		$this->localCode = $localCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return self
	 */
	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTaxLevel1(): string
	{
		return $this->taxLevel1;
	}

	/**
	 * @param string $taxLevel1
	 * @return self
	 */
	public function setTaxLevel1(string $taxLevel1): self
	{
		$this->taxLevel1 = $taxLevel1;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTaxLevel2(): string
	{
		return $this->taxLevel2;
	}

	/**
	 * @param string $taxLevel2
	 * @return self
	 */
	public function setTaxLevel2(string $taxLevel2): self
	{
		$this->taxLevel2 = $taxLevel2;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTaxLevel3(): string
	{
		return $this->taxLevel3;
	}

	/**
	 * @param string $taxLevel3
	 * @return self
	 */
	public function setTaxLevel3(string $taxLevel3): self
	{
		$this->taxLevel3 = $taxLevel3;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTaxLevel4(): string
	{
		return $this->taxLevel4;
	}

	/**
	 * @param string $taxLevel4
	 * @return self
	 */
	public function setTaxLevel4(string $taxLevel4): self
	{
		$this->taxLevel4 = $taxLevel4;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTaxLevel5(): string
	{
		return $this->taxLevel5;
	}

	/**
	 * @param string $taxLevel5
	 * @return self
	 */
	public function setTaxLevel5(string $taxLevel5): self
	{
		$this->taxLevel5 = $taxLevel5;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getShippingTaxCode(): string
	{
		return $this->shippingTaxCode;
	}

	/**
	 * @param string $shippingTaxCode
	 * @return self
	 */
	public function setShippingTaxCode(string $shippingTaxCode): self
	{
		$this->shippingTaxCode = $shippingTaxCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCountryCodes(): string
	{
		return $this->countryCodes;
	}

	/**
	 * @param string $countryCodes
	 * @return self
	 */
	public function setCountryCodes(string $countryCodes): self
	{
		$this->countryCodes = $countryCodes;
		return $this;
	}

	/**
	 * Returns the tax rate for a given tax level, unless the requested level is empty where we default to the first tax code.
	 *
	 * @param int $level Tax level we wish to get the code for.
	 * @return string
	 */
	public function getTaxLevelCode(int $level): string
	{
		$key = 'taxLevel' . $level;
		$code = $this->{$key};

		return '' !== $code ? $code : $this->taxLevel1;
	}
}