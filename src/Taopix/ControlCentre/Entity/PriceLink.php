<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\PriceLinkRepository;

#[ORM\Entity(repositoryClass: PriceLinkRepository::class), ORM\Table(name: "pricelink", schema: "controlcentre")]
#[ORM\Index(columns: ["componentcode"], name: "componentcode"), ORM\Index(columns: ["groupcode"], name: "groupcode"), ORM\Index(columns: ["parentid"], name: "parentid")]
#[ORM\Index(columns: ["productcode", "componentcode", "parentpath"], name: "pricecompound"), ORM\Index(columns: ["productcode"], name: "productcode")]
class PriceLink
{
	#[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO'), ORM\Column(name: "id", nullable: false)]
	private ?int $id = null;

	#[ORM\Column(name: "datecreated", nullable: false)]
	private ?DateTime $dateCreated = null;

	#[ORM\Column(name: "parentid", nullable: false)]
	private int $parentId = 0;

	#[ORM\Column(name: "companycode", length: 50, nullable: false)]
	private string $companyCode = '';

	#[ORM\Column(name: "productcode", length: 50, nullable: false)]
	private string $productCode = '';

	#[ORM\Column(name: "linkedproductcode", length: 50, nullable: false)]
	private string $linkedProductCode = '';

	#[ORM\Column(name: "groupcode", length: 50, nullable: false)]
	private string $groupCode = '';

	#[ORM\Column(name: "componentcode", length: 152, nullable: false)]
	private string $componentCode = '';

	#[ORM\Column(name: "parentpath", length: 400, nullable: false)]
	private string $parentPath = '';

	#[ORM\Column(name: "sectionpath", length: 400, nullable: false)]
	private string $sectionPath = '';

	#[ORM\Column(name: "sectioncode", length: 50, nullable: false)]
	private string $sectionCode = '';

	#[ORM\Column(name: "sortorder", nullable: false)]
	private int $sortOrder = 0;

	#[ORM\Column(name: "shoppingcarttype", nullable: false)]
	private int $shoppingCartType = 0;

	#[ORM\Column(name: "priceid", nullable: false)]
	private int $priceId = 0;

	#[ORM\Column(name: "priceinfo", length: 1024, nullable: false)]
	private string $priceInfo = '';

	#[ORM\Column(name: "pricedescription", length: 1024, nullable: false)]
	private string $priceDescription = '';

	#[ORM\Column(name: "inheritparentqty", type: Types::SMALLINT, nullable: false)]
	private int $inheritParentQty = 0;

	#[ORM\Column(name: "isdefault", nullable: false)]
	private bool $default = false;

	#[ORM\Column(name: "isvisible", nullable: false)]
	private bool $visible = true;

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
	 * @param int $id
	 * @return PriceLink
	 */
	public function setId(int $id): PriceLink
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
	 * @param DateTime $dateCreated
	 * @return PriceLink
	 */
	public function setDateCreated(DateTime $dateCreated): PriceLink
	{
		$this->dateCreated = $dateCreated;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getParentId(): int
	{
		return $this->parentId;
	}

	/**
	 * @param int $parentId
	 * @return PriceLink
	 */
	public function setParentId(int $parentId): PriceLink
	{
		$this->parentId = $parentId;
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
	 * @return PriceLink
	 */
	public function setCompanyCode(string $companyCode): PriceLink
	{
		$this->companyCode = $companyCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCode(): string
	{
		return $this->productCode;
	}

	/**
	 * @param string $productCode
	 * @return PriceLink
	 */
	public function setProductCode(string $productCode): PriceLink
	{
		$this->productCode = $productCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLinkedProductCode(): string
	{
		return $this->linkedProductCode;
	}

	/**
	 * @param string $linkedProductCode
	 * @return PriceLink
	 */
	public function setLinkedProductCode(string $linkedProductCode): PriceLink
	{
		$this->linkedProductCode = $linkedProductCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getGroupCode(): string
	{
		return $this->groupCode;
	}

	/**
	 * @param string $groupCode
	 * @return PriceLink
	 */
	public function setGroupCode(string $groupCode): PriceLink
	{
		$this->groupCode = $groupCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getComponentCode(): string
	{
		return $this->componentCode;
	}

	/**
	 * @param string $componentCode
	 * @return PriceLink
	 */
	public function setComponentCode(string $componentCode): PriceLink
	{
		$this->componentCode = $componentCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getParentPath(): string
	{
		return $this->parentPath;
	}

	/**
	 * @param string $parentPath
	 * @return PriceLink
	 */
	public function setParentPath(string $parentPath): PriceLink
	{
		$this->parentPath = $parentPath;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSectionPath(): string
	{
		return $this->sectionPath;
	}

	/**
	 * @param string $sectionPath
	 * @return PriceLink
	 */
	public function setSectionPath(string $sectionPath): PriceLink
	{
		$this->sectionPath = $sectionPath;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSectionCode(): string
	{
		return $this->sectionCode;
	}

	/**
	 * @param string $sectionCode
	 * @return PriceLink
	 */
	public function setSectionCode(string $sectionCode): PriceLink
	{
		$this->sectionCode = $sectionCode;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSortOrder(): int
	{
		return $this->sortOrder;
	}

	/**
	 * @param int $sortOrder
	 * @return PriceLink
	 */
	public function setSortOrder(int $sortOrder): PriceLink
	{
		$this->sortOrder = $sortOrder;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getShoppingCartType(): int
	{
		return $this->shoppingCartType;
	}

	/**
	 * @param int $shoppingCartType
	 * @return PriceLink
	 */
	public function setShoppingCartType(int $shoppingCartType): PriceLink
	{
		$this->shoppingCartType = $shoppingCartType;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPriceId(): int
	{
		return $this->priceId;
	}

	/**
	 * @param int $priceId
	 * @return PriceLink
	 */
	public function setPriceId(int $priceId): PriceLink
	{
		$this->priceId = $priceId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPriceInfo(): string
	{
		return $this->priceInfo;
	}

	/**
	 * @param string $priceInfo
	 * @return PriceLink
	 */
	public function setPriceInfo(string $priceInfo): PriceLink
	{
		$this->priceInfo = $priceInfo;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPriceDescription(): string
	{
		return $this->priceDescription;
	}

	/**
	 * @param string $priceDescription
	 * @return PriceLink
	 */
	public function setPriceDescription(string $priceDescription): PriceLink
	{
		$this->priceDescription = $priceDescription;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getInheritParentQty(): int
	{
		return $this->inheritParentQty;
	}

	/**
	 * @param int $inheritParentQty
	 * @return PriceLink
	 */
	public function setInheritParentQty(int $inheritParentQty): PriceLink
	{
		$this->inheritParentQty = $inheritParentQty;
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
	 * @return PriceLink
	 */
	public function setDefault(bool $default): PriceLink
	{
		$this->default = $default;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isVisible(): bool
	{
		return $this->visible;
	}

	/**
	 * @param bool $visible
	 * @return PriceLink
	 */
	public function setVisible(bool $visible): PriceLink
	{
		$this->visible = $visible;
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
	 * @return PriceLink
	 */
	public function setActive(bool $active): PriceLink
	{
		$this->active = $active;
		return $this;
	}


}