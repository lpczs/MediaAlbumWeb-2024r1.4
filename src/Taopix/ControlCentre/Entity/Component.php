<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\ComponentRepository;

#[ORM\Entity(repositoryClass: ComponentRepository::class), ORM\Table(name: "components", schema: "controlcentre")]
#[ORM\UniqueConstraint(name: "SINGULAR", columns: ["code"]), ORM\Index(columns: ["code"], name: "SINGULAR")]
#[ORM\Index(columns: ["keywordgroupheaderid"], name: "keywordgroupheaderid")]
class Component
{
	#[ORM\Id, ORM\GeneratedValue, ORM\Column(name: "id")]
	private ?int $id = null;
	
	#[ORM\Column(name: "datecreated", nullable: false)]
	private ?DateTime $dateCreated = null;
	
	#[ORM\Column(name: "datelastmodified", nullable: false)]
	private ?DateTime $dateLastModified = null;
	
	#[ORM\Column(name: "companycode", length: 50, nullable: false)]
	private string $companyCode = '';
	
	#[ORM\Column(name: "categorycode", length: 50, nullable: false)]
	private string $categoryCode = '';
	
	#[ORM\Column(name: "code", length: 152, nullable: false)]
	private string $code = '';
	
	#[ORM\Column(name: "localcode", length: 50, nullable: false)]
	private string $localCode = '';
	
	#[ORM\Column(name: "skucode", length: 50, nullable: false)]
	private string $skuCode = '';
	
	#[ORM\Column(name: "name", length: 1024, nullable: false)]
	private string $name = '';
	
	#[ORM\Column(name: "info", length: 1024, nullable: false)]
	private string $info = '';
	
	#[ORM\Column(name: "moreinfolinkurl", length: 100, nullable: false)]
	private string $moreInfoLinkUrl = '';
	
	#[ORM\Column(name: "moreinfolinktext", length: 1024, nullable: false)]
	private string $moreInfoLinkText = '';
	
	#[ORM\Column(name: "unitcost", type: Types::DECIMAL, precision: 10, scale: 4, nullable: false)]
	private string $unitCost = '0.0000';
	
	#[ORM\Column(name: "minimumpagecount", nullable: false)]
	private int $minimumPageCount = 0;
	
	#[ORM\Column(name: "maximumpagecount", nullable: false)]
	private int $maximumPageCount = 0;
	
	#[ORM\Column(name: "weight", type: Types::DECIMAL, precision: 10, scale: 4, nullable: false)]
	private string $weight = '0.0000';
	
	#[ORM\Column(name: "default", nullable: false)]
	private bool $default = true;
	
	#[ORM\Column(name: "keywordgroupheaderid", nullable: false)]
	private int $keywordGroupHeaderId = 0;
	
	#[ORM\Column(name: "orderfooterusesproductquantity", nullable: false)]
	private int $orderFooterUsesProductQuantity = 0;
	
	#[ORM\Column(name: "orderfootertaxlevel", nullable: false)]
	private int $orderFooterTaxLevel = 1;
	
	#[ORM\Column(name: "storewhennotselected", nullable: false)]
	private bool $storeWhenNotSelected = true;
	
	#[ORM\Column(name: "active", nullable: false)]
	private bool $active = false;

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
	 * @return DateTime|null
	 */
	public function getDateLastModified(): ?DateTime
	{
		return $this->dateLastModified;
	}

	/**
	 * @param DateTime|null $dateLastModified
	 * @return self
	 */
	public function setDateLastModified(?DateTime $dateLastModified): self
	{
		$this->dateLastModified = $dateLastModified;
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
	public function getCategoryCode(): string
	{
		return $this->categoryCode;
	}

	/**
	 * @param string $categoryCode
	 * @return self
	 */
	public function setCategoryCode(string $categoryCode): self
	{
		$this->categoryCode = $categoryCode;
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
	public function getSkuCode(): string
	{
		return $this->skuCode;
	}

	/**
	 * @param string $skuCode
	 * @return self
	 */
	public function setSkuCode(string $skuCode): self
	{
		$this->skuCode = $skuCode;
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
	public function getInfo(): string
	{
		return $this->info;
	}

	/**
	 * @param string $info
	 * @return self
	 */
	public function setInfo(string $info): self
	{
		$this->info = $info;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMoreInfoLinkUrl(): string
	{
		return $this->moreInfoLinkUrl;
	}

	/**
	 * @param string $moreInfoLinkUrl
	 * @return self
	 */
	public function setMoreInfoLinkUrl(string $moreInfoLinkUrl): self
	{
		$this->moreInfoLinkUrl = $moreInfoLinkUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMoreInfoLinkText(): string
	{
		return $this->moreInfoLinkText;
	}

	/**
	 * @param string $moreInfoLinkText
	 * @return self
	 */
	public function setMoreInfoLinkText(string $moreInfoLinkText): self
	{
		$this->moreInfoLinkText = $moreInfoLinkText;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUnitCost(): string
	{
		return $this->unitCost;
	}

	/**
	 * @param string $unitCost
	 * @return self
	 */
	public function setUnitCost(string $unitCost): self
	{
		$this->unitCost = $unitCost;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getMinimumPageCount(): int
	{
		return $this->minimumPageCount;
	}

	/**
	 * @param int $minimumPageCount
	 * @return self
	 */
	public function setMinimumPageCount(int $minimumPageCount): self
	{
		$this->minimumPageCount = $minimumPageCount;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getMaximumPageCount(): int
	{
		return $this->maximumPageCount;
	}

	/**
	 * @param int $maximumPageCount
	 * @return self
	 */
	public function setMaximumPageCount(int $maximumPageCount): self
	{
		$this->maximumPageCount = $maximumPageCount;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getWeight(): string
	{
		return $this->weight;
	}

	/**
	 * @param string $weight
	 * @return self
	 */
	public function setWeight(string $weight): self
	{
		$this->weight = $weight;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isDefault(): bool
	{
		return $this->default;
	}

	/**
	 * @param bool $default
	 * @return self
	 */
	public function setDefault(bool $default): self
	{
		$this->default = $default;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getKeywordGroupHeaderId(): int
	{
		return $this->keywordGroupHeaderId;
	}

	/**
	 * @param int $keywordGroupHeaderId
	 * @return self
	 */
	public function setKeywordGroupHeaderId(int $keywordGroupHeaderId): self
	{
		$this->keywordGroupHeaderId = $keywordGroupHeaderId;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getOrderFooterUsesProductQuantity(): int
	{
		return $this->orderFooterUsesProductQuantity;
	}

	/**
	 * @param int $orderFooterUsesProductQuantity
	 * @return self
	 */
	public function setOrderFooterUsesProductQuantity(int $orderFooterUsesProductQuantity): self
	{
		$this->orderFooterUsesProductQuantity = $orderFooterUsesProductQuantity;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getOrderFooterTaxLevel(): int
	{
		return $this->orderFooterTaxLevel;
	}

	/**
	 * @param int $orderFooterTaxLevel
	 * @return self
	 */
	public function setOrderFooterTaxLevel(int $orderFooterTaxLevel): self
	{
		$this->orderFooterTaxLevel = $orderFooterTaxLevel;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isStoreWhenNotSelected(): bool
	{
		return $this->storeWhenNotSelected;
	}

	/**
	 * @param bool $storeWhenNotSelected
	 * @return self
	 */
	public function setStoreWhenNotSelected(bool $storeWhenNotSelected): self
	{
		$this->storeWhenNotSelected = $storeWhenNotSelected;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->active;
	}

	/**
	 * @param bool $active
	 * @return self
	 */
	public function setActive(bool $active): self
	{
		$this->active = $active;
		return $this;
	}
}