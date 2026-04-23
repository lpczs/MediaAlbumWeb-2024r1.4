<?php

namespace Taopix\Connector\Taopix\Entity;

use Taopix\Core\Entity\AbstractEntity;


class Voucher extends AbstractEntity
{
	/**
	 * @var int
	 */
	private $id = 0;

	/**
	 * @var string
	 */
	private $dateCreated = '';

	/**
	 * @var string
	 */
	private $companyCode = '';

	/**
	 * @var string
	 */
	private $owner = '';

	/**
	 * @var string
	 */
	private $promotionCode = '';

	/**
	 * @var string
	 */
	private $code = '';

	/**
	 * @var int
	 */
	private $type = 0;

	/**
	 * @var int
	 */
	private $defaultDiscount = 0;	

	/**
	 * @var string
	 */
	private $name = '';	

	/**
	 * @var string
	 */
	private $description = '';	

	/**
	 * @var string
	 */
	private $startDate = '2000-01-01 00:00:00';

	/**
	 * @var string
	 */
	private $endDate = '2000-01-01 00:00:00';

	/**
	 * @var string
	 */
	private $productCode = '';

	/**
	 * @var string
	 */
	private $groupCode = '';

	/**
	 * @var int
	 */
	private $userID = 0;

	/**
	 * @var int
	 */
	private $hasProductGroup = 0;

	/**
	 * @var int
	 */
	private $minimumQty = 1;


	/**
	 * @var int
	 */
	private $maximumQty = 9999;

	/**
	 * @var int
	 */
	private $lockQty = 0;

	/**
	 * @var float
	 */
	private $minimumValue = 0.00;

	/**
	 * @var string
	 */
	private $repeatType = 'SINGLE';

	/**
	 * @var string
	 */
	private $discountSection = 'TOTAL';

	/**
	 * @var string
	 */
	private $discountType = 'VALUE';

	/**
	 * @var float
	 */
	private $discountValue = 0.00;

	/**
	 * @var int
	 */
	private $applicationMethod = 0;

	/**
	 * @var int
	 */
	private $maxQtyToApplyDiscountTo = 0;

	/**
	 * @var float
	 */
	private $sellPrice = 0.00;

	/**
	 * @var float
	 */
	private $agentFee = 0.00;

	/**
	 * @var int
	 */
	private $redeemedUserID = 0;

	/**
	 * @var string
	 */
	private $redeemedDate = '0000-00-00 00:00:00';

	/**
	 * @var int
	 */
	private $sessionRef = 0;

	/**
	 * @var int
	 */
	private $orderID = 0;

	/**
	 * @var int
	 */
	private $active = 1;

	/**
	 * @var float
	 */
	private $minOrderValue = 0.00;

	/**
	 * @var int
	 */
	private $minOrderValueIncTax = 0;

	/**
	 * @var int
	 */
	private $minOrderValueIncShipping = 0;


	/**
	 * Sets the id property.
	 *
	 * @param int $pID id to set.
	 * @return Voucher Voucher instance.
	 */
	public function setID(int $pID): Voucher
	{
		$this->id = $pID;
		return $this;
	}

	/**
	 * Returns the id value.
	 *
	 * @return int
	 */
	public function getID(): int
	{
		return $this->id;
	}


	/**
	 * Sets the datecreated property.
	 *
	 * @param string $pDateCreated Date Created to set.
	 * @return Voucher Voucher instance.
	 */
	public function setDateCreated(string $pDateCreated): Voucher
	{
		$this->dateCreated = $pDateCreated;
		return $this;
	}

	/**
	 * Returns the date created value.
	 *
	 * @return string datecreated value.
	 */
	public function getDateCreated(): string
	{
		return $this->dateCreated;
	}

	/**
	 * Sets the company code value.
	 *
	 * @param string $pCompanyCode Company code type to set.
	 * @return Voucher Voucher instance.
	 */
	public function setCompanyCode(string $pCompanyCode): Voucher
	{
		$this->companyCode = $pCompanyCode;
		return $this;
	}

	/**
	 * Returns the company code value.
	 *
	 * @return string Company code value.
	 */
	public function getCompanyCode(): string
	{
		return $this->companyCode;
	}

	/**
	 * Sets the owner property.
	 *
	 * @param string $pOwner owner to set.
	 * @return Voucher Voucher instance
	 */
	public function setOwner(string $pOwner): Voucher
	{
		$this->owner = $pOwner;
		return $this;
	}

	/**
	 * Returns the owner value.
	 *
	 * @return string Owner value.
	 */
	public function getOwner(): string
	{
		return $this->owner;
	}

	/**
	 * Sets the promotion code property.
	 *
	 * @param string $pPromotionCode Promotion Code to set.
	 * @return Voucher Voucher instance
	 */
	public function setPromotionCode(string $pPromotionCode): Voucher
	{
		$this->promotionCode = $pPromotionCode;
		return $this;
	}

	/**
	 * Returns the promotion code value.
	 *
	 * @return string Promotion code value.
	 */
	public function getPromotionCode(): string
	{
		return $this->promotionCode;
	}

	/**
	 * Sets the code property.
	 *
	 * @param string $pCode Voucher code value
	 * @return Voucher Voucher instance.
	 */
	public function setCode(string $pCode): Voucher
	{
		$this->code = $pCode;
		return $this;
	}

	/**
	 * Returns the code value.
	 *
	 * @return string Voucher code value
	 */
	public function getCode(): string
	{
		return $this->code;
	}	

	/**
	 * Sets the type property.
	 *
	 * @param int $pType Voucher type value.
	 * @return Voucher Voucher instance.
	 */
	public function setType(int $pType): Voucher
	{
		$this->type = $pType;
		return $this;
	}

	/**
	 * Returns the type value.
	 *
	 * @return int Voucher type value.
	 */
	public function getType(): int
	{
		return $this->type;
	}


	/**
	 * Sets default discount property.
	 *
	 * @param int $pDefaultDiscount Default discount value
	 * @return Voucher Voucher instance.
	 */
	public function setDefaultDiscount(int $pDefaultDiscount): Voucher
	{
		$this->defaultDiscount = $pDefaultDiscount;
		return $this;
	}

	/**
	 * Returns the default discount value.
	 *
	 * @return int Voucher default discount value.
	 */
	public function getDefaultDiscount(): int
	{
		return $this->defaultDiscount;
	}

	/**
	 * Sets name property.
	 *
	 * @param string $pName Name value
	 * @return Voucher Voucher instance.
	 */
	public function setName(string $pName): Voucher
	{
		$this->name = $pName;
		return $this;
	}

	/**
	 * Returns the name value.
	 *
	 * @return string Voucher name value.
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * Sets description property.
	 *
	 * @param string $pDescription Description value
	 * @return Voucher Voucher instance.
	 */
	public function setDescription(string $pDescription): Voucher
	{
		$this->description = $pDescription;
		return $this;
	}

	/**
	 * Returns the description value.
	 *
	 * @return string Voucher description value.
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * Sets startdate property.
	 *
	 * @param string $pStartDate Start date value
	 * @return Voucher Voucher instance.
	 */
	public function setStartDate(string $pStartDate): Voucher
	{
		$this->startDate = $pStartDate;
		return $this;
	}

	/**
	 * Returns the startdate value.
	 *
	 * @return string Voucher start date value.
	 */
	public function getStartDate(): string
	{
		return $this->startDate;
	}

	/**
	 * Sets enddate property.
	 *
	 * @param string $pEndDate End date value
	 * @return Voucher Voucher instance.
	 */
	public function setEndDate(string $pEndDate): Voucher
	{
		$this->endDate = $pEndDate;
		return $this;
	}

	/**
	 * Returns the end date value.
	 *
	 * @return string Voucher end date value.
	 */
	public function getEndDate(): string
	{
		return $this->endDate;
	}

	/**
	 * Sets productcode property.
	 *
	 * @param string $pProductCode Product code value
	 * @return Voucher Voucher instance.
	 */
	public function setProductCode(string $pProductCode): Voucher
	{
		$this->productCode = $pProductCode;
		return $this;
	}

	/**
	 * Returns the productcode value.
	 *
	 * @return string Voucher product code value.
	 */
	public function getProductCode(): string
	{
		return $this->productCode;
	}

	/**
	 * Sets groupcode property.
	 *
	 * @param string $pGroupCode Group code value
	 * @return Voucher Voucher instance.
	 */
	public function setGroupCode(string $pGroupCode): Voucher
	{
		$this->groupCode = $pGroupCode;
		return $this;
	}

	/**
	 * Returns the groupcode value.
	 *
	 * @return string Voucher group code value.
	 */
	public function getGroupCode(): string
	{
		return $this->groupCode;
	}

	/**
	 * Sets userid property.
	 *
	 * @param int $pUserID UserID value
	 * @return Voucher Voucher instance.
	 */
	public function setUserID(int $pUserID): Voucher
	{
		$this->userID = $pUserID;
		return $this;
	}

	/**
	 * Returns the userid value.
	 *
	 * @return int Voucher userid value.
	 */
	public function getUserID(): int
	{
		return $this->userID;
	}

	/**
	 * Returns the hasproductgroup value.
	 *
	 * @return int Voucher hasproductgroup value.
	 */
	public function getHasProductGroup() : int
	{
		return $this->hasProductGroup;
	}

	/**
	 * Sets hasproductgroup property.
	 *
	 * @param int $pHasProductGroup hasproductgroup value
	 * @return Voucher Voucher instance.
	 */
	public function setHasProductGroup(int $pHasProductGroup) : Voucher
	{
		$this->hasProductGroup = $pHasProductGroup;

		return $this;
	}

	/**
	 * Returns the minimumqty value.
	 *
	 * @return int Voucher minimumqty value.
	 */
	public function getMinimumQty() : int
	{
		return $this->minimumQty;
	}

	/**
	 * Sets minimumqty property.
	 *
	 * @param int $pMinimumQty minimumqty value
	 * @return Voucher Voucher instance.
	 */
	public function setMinimumQty(int $pMinimumQty) : Voucher
	{
		$this->minimumQty = $pMinimumQty;

		return $this;
	}

	/**
	 * Returns the maximumqty value.
	 *
	 * @return int Voucher maximumqty value.
	 */
	public function getMaximumQty() : int
	{
		return $this->maximumQty;
	}

	/**
	 * Sets maximumqty property.
	 *
	 * @param int $pMaximumQty maximumqty value
	 * @return Voucher Voucher instance.
	 */
	public function setMaximumQty(int $pMaximumQty) : Voucher
	{
		$this->maximumQty = $pMaximumQty;

		return $this;
	}

	/**
	 * Returns the lockqty value.
	 *
	 * @return int Voucher lockqty value.
	 */
	public function getLockQty() : int
	{
		return $this->lockQty;
	}

	/**
	 * Sets lockqty property.
	 *
	 * @param int $pLockQty lockqty value
	 * @return Voucher Voucher instance.
	 */
	public function setLockQty(int $pLockQty) : Voucher
	{
		$this->lockQty = $pLockQty;

		return $this;
	}

	/**
	 * Returns the minimumvalue value.
	 *
	 * @return int Voucher minimumvalue value.
	 */ 
	public function getMinimumValue() : float
	{
		return $this->minimumValue;
	}

	/**
	 * Sets minimumvalue property.
	 *
	 * @param float $pMinimumValue minimumvalue value
	 * @return Voucher Voucher instance.
	 */
	public function setMinimumValue(float $pMinimumValue) : Voucher
	{
		$this->minimumValue = $pMinimumValue;

		return $this;
	}

	/**
	 * Returns the repeattype value.
	 *
	 * @return string Voucher repeattype value.
	 */ 
	public function getRepeatType() : string
	{
		return $this->repeatType;
	}

	/**
	 * Sets repeattype property.
	 *
	 * @param string $pRepeatType repeattype value
	 * @return Voucher Voucher instance.
	 */
	public function setRepeatType(string $pRepeatType) : Voucher
	{
		$this->repeatType = $pRepeatType;

		return $this;
	}

	/**
	 * Returns the discountsection value.
	 *
	 * @return string Voucher discountsection value.
	 */ 
	public function getDiscountSection() : string
	{
		return $this->discountSection;
	}

	/**
	 * Sets discountsection property.
	 *
	 * @param string $pDiscountSection discountsection value
	 * @return Voucher Voucher instance.
	 */
	public function setDiscountSection(string $pDiscountSection) : Voucher
	{
		$this->discountSection = $pDiscountSection;

		return $this;
	}

	/**
	 * Returns the discounttype value.
	 *
	 * @return string Voucher discounttype value.
	 */ 
	public function getDiscountType() : string
	{
		return $this->discountType;
	}

	/**
	 * Sets discounttype property.
	 *
	 * @param string $pDiscountType discounttype value
	 * @return Voucher Voucher instance.
	 */
	public function setDiscountType(string $pDiscountType) : Voucher
	{
		$this->discountType = $pDiscountType;

		return $this;
	}

	/**
	 * Returns the discountvalue value.
	 *
	 * @return float Voucher discountvalue value.
	 */ 
	public function getDiscountValue() : float
	{
		return $this->discountValue;
	}

	/**
	 * Sets discountvalue property.
	 *
	 * @param float $pDiscountValue discountvalue value
	 * @return Voucher Voucher instance.
	 */
	public function setDiscountValue(float $pDiscountValue) : Voucher
	{
		$this->discountValue = $pDiscountValue;

		return $this;
	}

	/**
	 * Returns the applicationmethod value.
	 *
	 * @return int Voucher applicationmethod value.
	 */ 
	public function getApplicationMethod() : int
	{
		return $this->applicationMethod;
	}

	/**
	 * Sets applicationmethod property.
	 *
	 * @param int $pApplicationMethod applicationmethod value
	 * @return Voucher Voucher instance.
	 */
	public function setApplicationMethod(int $pApplicationMethod) : Voucher
	{
		$this->applicationMethod = $pApplicationMethod;

		return $this;
	}

	/**
	 * Returns the maxqtytoapplydiscountto value.
	 *
	 * @return int Voucher maxqtytoapplydiscountto value.
	 */ 
	public function getMaxQtyToApplyDiscountTo() : int
	{
		return $this->maxQtyToApplyDiscountTo;
	}

	/**
	 * Sets maxqtytoapplydiscountto property.
	 *
	 * @param int $pMaxQtyToApplyDiscountTo maxqtytoapplydiscountto value
	 * @return Voucher Voucher instance.
	 */
	public function setMaxQtyToApplyDiscountTo(int $pMaxQtyToApplyDiscountTo) : Voucher
	{
		$this->maxQtyToApplyDiscountTo = $pMaxQtyToApplyDiscountTo;

		return $this;
	}

	/**
	 * Returns the sellprice value.
	 *
	 * @return float Voucher sellprice value.
	 */ 
	public function getSellPrice() : float
	{
		return $this->sellPrice;
	}

	/**
	 * Sets sellprice property.
	 *
	 * @param float $pSellPrice sellprice value
	 * @return Voucher Voucher instance.
	 */
	public function setSellPrice(float $pSellPrice) : Voucher
	{
		$this->sellPrice = $pSellPrice;

		return $this;
	}

	/**
	 * Returns the agentfee value.
	 *
	 * @return float Voucher agentfee value.
	 */ 
	public function getAgentFee() : float
	{
		return $this->agentFee;
	}

	/**
	 * Sets agentfee property.
	 *
	 * @param float $pAgentFee agentfee value
	 * @return Voucher Voucher instance.
	 */
	public function setAgentFee(float $pAgentFee) : Voucher
	{
		$this->agentFee = $pAgentFee;

		return $this;
	}

	/**
	 * Returns the redeemeduserid value.
	 *
	 * @return int Voucher redeemeduserid value.
	 */ 
	public function getRedeemedUserID() : int
	{
		return $this->redeemedUserID;
	}

	/**
	 * Sets redeemeduserid property.
	 *
	 * @param int $pAgentFee redeemeduserid value
	 * @return Voucher Voucher instance.
	 */
	public function setRedeemedUserID(int $pRedeemedUserID) : Voucher
	{
		$this->redeemedUserID = $pRedeemedUserID;

		return $this;
	}

	/**
	 * Returns the redeemeddate property.
	 *
	 * @return string Voucher redeemeddate value.
	 */ 
	public function getRedeemedDate() : string
	{
		return $this->redeemedDate;
	}

	/**
	 * Sets redeemeddate property.
	 *
	 * @param string $pRedeemedDate redeemeddate value
	 * @return Voucher Voucher instance.
	 */
	public function setRedeemedDate(string $pRedeemedDate) : Voucher
	{
		$this->redeemedDate = $pRedeemedDate;

		return $this;
	}

	/**
	 * Returns the sessionref property.
	 *
	 * @return int Voucher sessionref value.
	 */ 
	public function getSessionRef(): int
	{
		return $this->sessionRef;
	}

	/**
	 * Sets sessionref property.
	 *
	 * @param int $pSessionRef sessionref value
	 * @return Voucher Voucher instance.
	 */
	public function setSessionRef(int $pSessionRef) : Voucher
	{
		$this->sessionRef = $pSessionRef;

		return $this;
	}

	/**
	 * Returns the orderid property.
	 *
	 * @return int Voucher orderid value.
	 */ 
	public function getOrderID() : int
	{
		return $this->orderID;
	}

	/**
	 * Sets orderid property.
	 *
	 * @param int $pOrderID orderid value
	 * @return Voucher Voucher instance.
	 */ 
	public function setOrderID(int $pOrderID) : Voucher
	{
		$this->orderID = $pOrderID;

		return $this;
	}

	/**
	 * Returns the active property.
	 *
	 * @return int Voucher active value.
	 */ 
	public function getActive() : int
	{
		return $this->active;
	}

	/**
	 * Sets active property.
	 *
	 * @param int $pActive active value
	 * @return Voucher Voucher instance.
	 */ 
	public function setActive(int $pActive) : Voucher
	{
		$this->active = $pActive;

		return $this;
	}

	/**
	 * Returns the minordervalue property.
	 *
	 * @return float Voucher minordervalue value.
	 */ 
	public function getMinOrderValue() : float
	{
		return $this->minOrderValue;
	}

	/**
	 * Sets minordervalue property.
	 *
	 * @param float $pMinOrderValue minordervalue value
	 * @return Voucher Voucher instance.
	 */ 
	public function setMinOrderValue(float $pMinOrderValue) : Voucher
	{
		$this->minOrderValue = $pMinOrderValue;

		return $this;
	}

	/**
	 * Returns the minordervalueinctax property.
	 *
	 * @return int Voucher minordervalueinctax value.
	 */ 
	public function getMinOrderValueIncTax() : int
	{
		return $this->minOrderValueIncTax;
	}

	/**
	 * Sets minordervalueinctax property.
	 *
	 * @param int $pMinOrderValueIncTax minordervalueinctax value
	 * @return Voucher Voucher instance.
	 */ 
	public function setMinOrderValueIncTax(int $pMinOrderValueIncTax) : Voucher
	{
		$this->minOrderValueIncTax = $pMinOrderValueIncTax;

		return $this;
	}

	/**
	 * Returns the minordervalueincshipping property.
	 *
	 * @return int Voucher minordervalueincshipping value.
	 */ 
	public function getMinOrderValueIncShipping() : int
	{
		return $this->minOrderValueIncShipping;
	}

	/**
	 * Sets minordervalueincshipping property.
	 *
	 * @param int $pMinOrderValueIncShipping minordervalueincshipping value
	 * @return Voucher Voucher instance.
	 */ 
	public function setMinOrderValueIncShipping(int $pMinOrderValueIncShipping) : Voucher
	{
		$this->minOrderValueIncShipping = $pMinOrderValueIncShipping;

		return $this;
	}

	/**
	 * Returns object properties as an array.
	 *
	 * @return array Properties array.
	 */
	public function getProperties(): array
	{
		$properties = get_object_vars($this);

		$properties = array_change_key_case($properties, CASE_LOWER);

		return $properties;
	}
}
