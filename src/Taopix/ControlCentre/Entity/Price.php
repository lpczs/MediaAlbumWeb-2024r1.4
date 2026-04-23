<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Enum\Price\Model;
use Taopix\ControlCentre\Repository\PriceRepository;

#[ORM\Entity(repositoryClass: PriceRepository::class), ORM\Table(name: "prices", schema: "controlcentre"), ORM\UniqueConstraint(name: "pricelistcode", columns: ["pricelistcode"])]
#[ORM\Index(columns: ["pricelistcode"], name: "pricelistcode")]
class Price
{
	#[ORM\Id, ORM\GeneratedValue(strategy: "AUTO"), ORM\Column(name: "id", nullable: false)]
	private ?int $id = null;

	#[ORM\Column(name: "datecreated", nullable: false)]
	private ?DateTime $dateCreated = null;

	#[ORM\Column(name: "companycode", length: 50, nullable: false)]
	private string $companyCode = '';

	#[ORM\Column(name: "categorycode", length: 50, nullable: false)]
	private string $categoryCode = '';

	#[ORM\Column(name: "linkedpricelistid", nullable: false)]
	private int $linkedPriceListId = 0;

	#[ORM\Column(name: "pricingmodel", nullable: false)]
	private int $pricingModel = 0;

	#[ORM\Column(name: "price", length: 2048, nullable: false)]
	private string $price = '';

	#[ORM\Column(name: "pricelistcode", length: 50, nullable: false)]
	private string $priceListCode = '';

	#[ORM\Column(name: "pricelistlocalcode", length: 50, nullable: false)]
	private string $priceListLocalCode = '';

	#[ORM\Column(name: "pricelistname", length: 50, nullable: false)]
	private string $priceListName = '';

	#[ORM\Column(name: "quantityisdropdown", nullable: false)]
	private bool $quantityIsDropDown = false;

	#[ORM\Column(name: "ispricelist", nullable: false)]
	private bool $priceList = false;

	#[ORM\Column(name: "taxcode", length: 20, nullable: false)]
	private string $taxCode = '';

	#[ORM\Column(name: "active", nullable: false)]
	private bool $active = false;

	private array $formattedPrices = [];

	private array $quantityDropDown = [];

	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return Price
	 */
	public function setId(int $id): Price
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
	 * @return Price
	 */
	public function setDateCreated(DateTime $dateCreated): Price
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
	 * @return Price
	 */
	public function setCompanyCode(string $companyCode): Price
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
	 * @return Price
	 */
	public function setCategoryCode(string $categoryCode): Price
	{
		$this->categoryCode = $categoryCode;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getLinkedPriceListId(): int
	{
		return $this->linkedPriceListId;
	}

	/**
	 * @param int $linkedPriceListId
	 * @return Price
	 */
	public function setLinkedPriceListId(int $linkedPriceListId): Price
	{
		$this->linkedPriceListId = $linkedPriceListId;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPricingModel(): int
	{
		return $this->pricingModel;
	}

	/**
	 * @param int $pricingModel
	 * @return Price
	 */
	public function setPricingModel(int $pricingModel): Price
	{
		$this->pricingModel = $pricingModel;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrice(): string
	{
		return $this->price;
	}

	/**
	 * @param string $price
	 * @return Price
	 */
	public function setPrice(string $price): Price
	{
		$this->price = $price;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPriceListCode(): string
	{
		return $this->priceListCode;
	}

	/**
	 * @param string $priceListCode
	 * @return Price
	 */
	public function setPriceListCode(string $priceListCode): Price
	{
		$this->priceListCode = $priceListCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPriceListLocalCode(): string
	{
		return $this->priceListLocalCode;
	}

	/**
	 * @param string $priceListLocalCode
	 * @return Price
	 */
	public function setPriceListLocalCode(string $priceListLocalCode): Price
	{
		$this->priceListLocalCode = $priceListLocalCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPriceListName(): string
	{
		return $this->priceListName;
	}

	/**
	 * @param string $priceListName
	 * @return Price
	 */
	public function setPriceListName(string $priceListName): Price
	{
		$this->priceListName = $priceListName;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isQuantityIsDropDown(): bool
	{
		return $this->quantityIsDropDown;
	}

	/**
	 * @param bool $quantityIsDropDown
	 * @return Price
	 */
	public function setQuantityIsDropDown(bool $quantityIsDropDown): Price
	{
		$this->quantityIsDropDown = $quantityIsDropDown;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isPriceList(): bool
	{
		return $this->priceList;
	}

	/**
	 * @param bool $priceList
	 * @return Price
	 */
	public function setPriceList(bool $priceList): Price
	{
		$this->priceList = $priceList;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTaxCode(): string
	{
		return $this->taxCode;
	}

	/**
	 * @param string $taxCode
	 * @return Price
	 */
	public function setTaxCode(string $taxCode): Price
	{
		$this->taxCode = $taxCode;
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
	 * @return Price
	 */
	public function setActive(bool $active): Price
	{
		$this->active = $active;
		return $this;
	}

	// TODO: Add formatted price function to return a formatted array.

	public function unpackPriceString()
	{
		if (!empty($this->formattedPrices)) {
			return $this->formattedPrices;
		}

		$priceLines = explode(' ', $this->price);
		$priceFormat = [
			'startqty' => 0,
			'endqty' => 0,
			'startcmpqty' => 0,
			'endcmpqty' => 0,
			'startpagecount' => 0,
			'endpagecount' => 0,
			'baseprice' => 0.00,
			'unitsell' => 0.00,
			'linesubtract' => 0.00
		];

		foreach ($priceLines as $key => $priceLine) {
			if ('' === trim($priceLine)) {
				continue;
			}

			$priceDetails = explode('*', trim($priceLine));

			$this->formattedPrices[] = match (Model::tryFrom($this->pricingModel)) {
				Model::PER_QTY => array_merge($priceFormat, [
					'startqty' => $priceDetails[0],
					'endqty' => $priceDetails[1],
					'baseprice' => $priceDetails[2],
					'unitsell' => (float) $priceDetails[3],
					'linesubtract' => $priceDetails[4],
				]),
				Model::PER_SIDE_QTY => array_merge($priceFormat, [
					'startqty' => $priceDetails[0],
					'endqty' => $priceDetails[1],
					'startpagecount' => $priceDetails[2],
					'endpagecount' => $priceDetails[3],
					'baseprice' => $priceDetails[4],
					'unitsell' => (float) $priceDetails[5],
					'linesubtract' => $priceDetails[6],
				]),
				Model::PER_PRODUCT_COMPONENT_QTY => array_merge($priceFormat, [
					'startqty' => $priceDetails[0],
					'endqty' => $priceDetails[1],
					'startcmpqty' => $priceDetails[2],
					'endcmpqty' => $priceDetails[3],
					'baseprice' => $priceDetails[4],
					'unitsell' => (float) $priceDetails[5],
					'linesubtract' => $priceDetails[6],
				]),
				Model::PER_SIDE_PER_PRODUCT_COMPONENT_QTY => array_merge($priceFormat, [
					'startqty' => $priceDetails[0],
					'endqty' => $priceDetails[1],
					'startpagecount' => $priceDetails[2],
					'endpagecount' => $priceDetails[3],
					'startpagecount' => $priceDetails[4],
					'endpagecount' => $priceDetails[5],
					'baseprice' => $priceDetails[6],
					'unitsell' => (float) $priceDetails[7],
					'linesubtract' => $priceDetails[8],
				]),
			};
		}

		return $this->formattedPrices;
	}

	public function generateItemQuantityValues()
	{
		if (!$this->quantityIsDropDown) {
			return [];
		}

		if (!empty($this->quantityDropDown)) {
			return $this->quantityDropDown;
		}

		$this->unpackPriceString();

		if (!empty($this->formattedPrices)) {

		}

		return $this->quantityDropDown;
	}
}
