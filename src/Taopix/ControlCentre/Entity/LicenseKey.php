<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedPath;
use Taopix\ControlCentre\Traits\Entity\ToArrayTrait;
use Taopix\ControlCentre\Repository\LicenseKeyRepository;

#[ORM\Entity(repositoryClass: LicenseKeyRepository::class), ORM\Table(name: "licensekeys", schema: "controlcentre")]
#[ORM\UniqueConstraint(name: "groupcode", columns: ["groupcode"]), ORM\Index(columns: ["taxcode"], name: "taxcode"), ORM\Index(columns: ["shippingtaxcode"], name: "shippingtaxcode")]
class LicenseKey
{
	use ToArrayTrait;

	#[ORM\Column(name: "id", nullable: false), ORM\Id(), ORM\GeneratedValue()]
    private ?int $id = null;

	#[ORM\Column(name: "datecreated", nullable: false)]
    private ?DateTime $dateCreated = null;

	#[ORM\Column(name: "companycode", length: 50, nullable: false), Groups(['license-details'])]
    private string $companyCode = '';

	#[ORM\Column(name: "groupcode", length: 50, nullable: false), SerializedPath('[code]'), Groups(['license-details'])]
    private string $groupCode = '';

	#[ORM\Column(name: "name", length: 200, nullable: false)]
    private string $name = '';

	#[ORM\Column(name: "address1", length: 200, nullable: false)]
    private string $address1 = '';

	#[ORM\Column(name: "address2", length: 200, nullable: false)]
    private string $address2 = '';

	#[ORM\Column(name: "address3", length: 200, nullable: false)]
    private string $address3 = '';

	#[ORM\Column(name: "address4", length: 200, nullable: false)]
    private string $address4 = '';

	#[ORM\Column(name: "city", length: 200, nullable: false)]
    private string $city = '';

	#[ORM\Column(name: "county", length: 50, nullable: false)]
    private string $county = '';

	#[ORM\Column(name: "state", length: 200, nullable: false)]
    private string $state = '';

	#[ORM\Column(name: "regioncode", length: 20, nullable: false)]
    private string $regionCode = '';

	#[ORM\Column(name: "region", length: 10, nullable: false)]
    private string $region = '';

	#[ORM\Column(name: "postcode", length: 200, nullable: false)]
    private string $postCode = '';

	#[ORM\Column(name: "countrycode", length: 10, nullable: false)]
    private string $countryCode = '';

	#[ORM\Column(name: "countryname", length: 50, nullable: false)]
    private string $countryName = '';

	#[ORM\Column(name: "telephonenumber", length: 50, nullable: false)]
    private string $telephoneNumber = '';

	#[ORM\Column(name: "emailaddress", length: 50, nullable: false)]
    private string $emailAddress = '';

	#[ORM\Column(name: "contactfirstname", length: 200, nullable: false)]
    private string $contactFirstName = '';

	#[ORM\Column(name: "contactlastname", length: 200, nullable: false)]
    private string $contactLastName = '';

	#[ORM\Column(name: "createaccounts", nullable: false), SerializedPath('[canCreateAccounts]'), Groups(['license-details'])]
    private bool $createAccounts = false;

	#[ORM\Column(name: "useaddressforbilling", nullable: false)]
    private bool $useAddressForBilling = false;

	#[ORM\Column(name: "useaddressforshipping", nullable: false)]
    private bool $useAddressForShipping = false;

	#[ORM\Column(name: "modifyshippingaddress", nullable: false)]
    private bool $modifyShippingAddress = true;

	#[ORM\Column(name: "modifybillingaddress", nullable: false)]
    private bool $modifyBillingAddress = true;

	#[ORM\Column(name: "modifyshippingcontactdetails", nullable: false)]
    private bool $modifyShippingContactDetails = false;

	#[ORM\Column(name: "useremaildestination", nullable: false)]
    private bool $userEmailDestination = false;

	#[ORM\Column(name: "orderfrompreview", type: Types::SMALLINT, nullable: false), SerializedPath('[general][orderFromPreview]'), Groups(['license-details'])]
    private int $orderFromPreview = 2;

	#[ORM\Column(name: "showpriceswithtax", nullable: false), SerializedPath('[taxSettings][withTax]'), Groups(['license-details'])]
    private bool $showPricesWithTax = true;

	#[ORM\Column(name: "showtaxbreakdown", nullable: false), SerializedPath('[taxSettings][showBreakDown]'), Groups(['license-details'])]
    private bool $showTaxBreakDown = true;

	#[ORM\Column(name: "showzerotax", nullable: false), SerializedPath('[taxSettings][showZeroTax]'), Groups(['license-details'])]
    private bool $showZeroTax = true;

	#[ORM\Column(name: "showalwaystaxtotal", nullable: false), SerializedPath('[taxSettings][alwaysTotal]'), Groups(['license-details'])]
    private bool $showAlwaysTaxTotal = false;

	#[ORM\Column(name: "login", length: 50, nullable: false)]
    private string $login = '';

	#[ORM\Column(name: "password", length: 50, nullable: false)]
    private string $password = '';

	#[ORM\Column(name: "keyfilename", length: 100, nullable: false)]
    private string $keyFileName = '';

	#[ORM\Column(name: "keyfilenameversion", nullable: false)]
    private ?DateTime $keyFileNameVersion = null;

	#[ORM\Column(name: "keyfilesize", length: 50, nullable: false, options: ["unsigned" => true])]
    private int $keyFileSize = 0;

	#[ORM\Column(name: "keyfilechecksum", length: 255, nullable: false)]
    private string $keyFileCheckSum = '';

	#[ORM\Column(name: "keyupdatepriority", nullable: false)]
    private int $keyUpdatePriority = 0;

	#[ORM\Column(name: "webbrandcode", length: 50, nullable: false), SerializedPath('[brandCode]'), Groups(['license-details'])]
    private string $webBrandCode = '';

	#[ORM\Column(name: "usedefaultcurrency", nullable: false)]
    private bool $useDefaultCurrency = true;

	#[ORM\Column(name: "currencycode", length: 20, nullable: false)]
    private string $currencyCode = '';

	#[ORM\Column(name: "taxcode", length: 20, nullable: false)]
    private string $taxCode = '';

	#[ORM\Column(name: "shippingtaxcode", length: 50, nullable: false)]
    private string $shippingTaxCode = '';

	#[ORM\Column(name: "registeredtaxnumbertype", type: Types::SMALLINT, nullable: false)]
    private int $registeredTaxNumberType = 0;

	#[ORM\Column(name: "registeredtaxnumber", length: 50, nullable: false)]
    private string $registeredTaxNumber = '';

	#[ORM\Column(name: "usedefaultpaymentmethods", nullable: false)]
    private bool $useDefaultPaymentMethods = true;

	#[ORM\Column(name: "paymentmethods", length: 100, nullable: false)]
    private string $paymentMethods = '';

	#[ORM\Column(name: "designersplashscreenassetid", nullable: false)]
    private int $designerSplashScreenAssetId = 0;

	#[ORM\Column(name: "designersplashscreenstartdate", nullable: false)]
    private ?DateTime $designerSplashScreenStartDate = null;

	#[ORM\Column(name: "designersplashscreenenddate", nullable: false)]
    private ?DateTime $designerSplashScreenEndDate = null;

	#[ORM\Column(name: "designerbannerassetid", length: 50, nullable: false)]
    private int $designerBannerAssetId = 0;

	#[ORM\Column(name: "designerbannerstartdate", nullable: false)]
    private ?DateTime $designerBannerStartDate = null;

	#[ORM\Column(name: "designerbannerenddate", length: 50, nullable: false)]
    private ?DateTime $designerBannerEndDate = null;

	#[ORM\Column(name: "onlinedesignerguestworkflowmode", type: Types::SMALLINT, nullable: false), SerializedPath('[general][guestWorkflow]'), Groups(['license-details'])]
    private int $onlineDesignerGuestWorkflowMode = 0;

	#[ORM\Column(name: "maxorderbatchsize", nullable: false)]
    private int $maxOrderBatchSize = 1;

	#[ORM\Column(name: "active", nullable: false)]
    private bool $active = false;

	#[ORM\Column(name: "availableonline", nullable: false)]
    private bool $availableOnline = false;

	#[ORM\Column(name: "cacheversion", length: 30, nullable: false), Groups(['license-details'])]
    private string $cacheVersion = '';

	#[ORM\Column(name: "imagescalingbefore", type: Types::DECIMAL, precision: 5, scale: 2, nullable: false, options: ["default" => "0.00"])]
    #[SerializedPath('[imageScaling][before][maxMp]'), Groups(['license-details'])]
    private string $imageScalingBefore = '0.00';

	#[ORM\Column(name: "imagescalingbeforeenabled", nullable: false), SerializedPath('[imageScaling][before][enabled]'), Groups(['license-details'])]
    private bool $imageScalingBeforeEnabled = false;

	#[ORM\Column(name: "usedefaultimagescalingbefore", nullable: false), SerializedPath('[imageScaling][before][useDefault]'), Groups(['license-details'])]
    private bool $useDefaultImageScalingBefore = true;

	#[ORM\Column(name: "imagescalingafter", type: Types::DECIMAL, precision: 5, scale: 2, nullable: false, options: ["default" => "0.00"])]
    #[SerializedPath('[imageScaling][after][maxMp]'), Groups(['license-details'])]
    private string $imageScalingAfter = '0.00';

	#[ORM\Column(name: "imagescalingafterenabled", nullable: false), SerializedPath('[imageScaling][after][enabled]'), Groups(['license-details'])]
    private bool $imageScalingAfterEnabled = false;

	#[ORM\Column(name: "usedefaultimagescalingafter", nullable: false), SerializedPath('[imageScaling][after][useDefault]'), Groups(['license-details'])]
    private bool $useDefaultImageScalingAfter = true;

	#[ORM\Column(name: "shufflelayout", nullable: false), SerializedPath('[shuffle][options]'), Groups(['license-details'])]
    private int $shuffleLayout = 0;

	#[ORM\Column(name: "showshufflelayoutoptions", nullable: false), SerializedPath('[shuffle][enabled]'), Groups(['license-details'])]
    private bool $showShuffleLayoutOptions = false;

	#[ORM\Column(name: "usedefaultshufflelayout", nullable: false), SerializedPath('[shuffle][useDefault]'), Groups(['license-details'])]
    private bool $useDefaultShuffleLayout = true;

	#[ORM\Column(name: "onlineeditormode", type: Types::SMALLINT, nullable: false), SerializedPath('[editor][settings][default]'), Groups(['license-details'])]
    private int $onlineEditorMode = 0;

	#[ORM\Column(name: "enableswitchingeditor", nullable: false), SerializedPath('[editor][settings][canSwitch]'), Groups(['license-details'])]
    private bool $enableSwitchingEditor = false;

	#[ORM\Column(name: "usedefaultonlineeditormode", nullable: false), SerializedPath('[editor][useDefault]'), Groups(['license-details'])]
    private bool $useDefaultOnlineEditorMode = true;

	#[ORM\Column(name: "onlinedesignerlogolinkurl", length: 100, nullable: false), SerializedPath('[general][logoLink][url]'), Groups(['license-details'])]
    private string $onlineDesignerLogoLinkUrl = '';

	#[ORM\Column(name: "usedefaultonlinedesignerlogolinkurl", nullable: false), SerializedPath('[general][logoLink][useDefault]'), Groups(['license-details'])]
    private bool $useDefaultOnlineDesignerLogoLinkUrl = true;

	#[ORM\Column(name: "onlinedesignerlogolinktooltip", length: 1024, nullable: false), SerializedPath('[general][logoLink][toolTip]'), Groups(['license-details'])]
    private string $onlineDesignerLogoLinkTooltip = '';

	#[ORM\Column(name: "usedefaultvouchersettings", nullable: false)]
    private bool $useDefaultVoucherSettings = true;

	#[ORM\Column(name: "allowvouchers", nullable: false)]
    private bool $allowVouchers = true;

	#[ORM\Column(name: "allowgiftcards", nullable: false)]
    private bool $allowGiftCards = true;

	#[ORM\Column(name: "usedefaultsizeandpositionsettings", nullable: false)]
    private bool $useDefaultSizeAndPositionSettings = true;

	#[ORM\Column(name: "sizeandpositionmeasurementunits", type: Types::SMALLINT, nullable: false)]
    private int $sizeAndPositionMeasurementUnits = 0;

	#[ORM\Column(name: "smartguidesenable", nullable: false), SerializedPath('[smartGuides][enabled]'), Groups(['license-details'])]
    private bool $smartGuidesEnable = true;

	#[ORM\Column(name: "smartguidesobjectguidecolour", length: 6, nullable: false, options: ["default" => "00CCFF"]), SerializedPath('[smartGuides][colours][object]'), Groups(['license-details'])]
    private string $smartGuidesObjectGuideColour = '00CCFF';

	#[ORM\Column(name: "smartguidespageguidecolour", length: 6, nullable: false, options: ["default" => "FF00FF"]), SerializedPath('[smartGuides][colours][page]'), Groups(['license-details'])]
    private string $smartGuidesPageGuideColour = 'FF00FF';

	#[ORM\Column(name: "usedefaultsmartguidessettings", nullable: false), SerializedPath('[smartGuides][useDefault]'), Groups(['license-details'])]
    private bool $useDefaultSmartGuidesSettings = true;

	#[ORM\Column(name: "usedefaultautomaticallyapplyperfectlyclear", nullable: false), SerializedPath('[autoEnhance][useDefault]'), Groups(['license-details'])]
    private bool $useDefaultAutomaticallyApplyPerfectlyClear = false;

	#[ORM\Column(name: "automaticallyapplyperfectlyclear", nullable: false), SerializedPath('[autoEnhance][mode]'), Groups(['license-details'])]
    private bool $automaticallyApplyPerfectlyClear = false;

	#[ORM\Column(name: "allowuserstotoggleperfectlyclear", nullable: false), SerializedPath('[autoEnhance][canToggle]'), Groups(['license-details'])]
    private bool $allowUsersToTogglePerfectlyClear = false;

	#[ORM\Column(name: "usedefaultinsertdeletebuttonsvisibility", nullable: false), SerializedPath('[pages][insertDelete][useDefault]'), Groups(['license-details'])]
    private bool $useDefaultInsertDeleteButtonsVisibility = true;

	#[ORM\Column(name: "insertdeletebuttonsvisibility", nullable: false), SerializedPath('[pages][insertDelete][settings]'), Groups(['license-details'])]
    private bool $insertDeleteButtonsVisibility = true;

	#[ORM\Column(name: "usedefaulttotalpagesdropdownmode", nullable: false), SerializedPath('[pages][totalPagesDropDown][useDefault]'), Groups(['license-details'])]
    private bool $useDefaultTotalPagesDropDownMode = true;

	#[ORM\Column(name: "totalpagesdropdownmode", type: Types::SMALLINT, nullable: false), SerializedPath('[pages][totalPagesDropDown][settings]'), Groups(['license-details'])]
    private int $totalPagesDropDownMode = 1;

	#[ORM\Column(name: "usedefaultaveragepicturesperpage", nullable: false), SerializedPath('[pages][averagePictures][useDefault]'), Groups(['license-details'])]
	private bool $useDefaultAveragePicturesPerPage = true;

	#[ORM\Column(name: "averagepicturesperpage", nullable: false, options: ["unsigned" => true]), SerializedPath('[pages][averagePictures][settings]'), Groups(['license-details'])]
	private int $averagePicturesPerPage = 0;

	#[ORM\Column(name: "usedefaultcomponentupsellsettings", nullable: false), SerializedPath('[general][componentUpsell][useDefault]'), Groups(['license-details'])]
	private bool $useDefaultComponentUpsellSettings = true;

	#[ORM\Column(name: "componentupsellsettings", nullable: false), SerializedPath('[general][componentUpsell][settings]'), Groups(['license-details'])]
	private int $componentUpsellSettings = 3;

    #[ORM\Column(name: 'usedefaultaccountpagesurl', nullable: false)]
    private bool $useDefaultAccountPagesUrl = true;

    #[ORM\Column(name: 'accountpagesurl', length: 100)]
    private string $accountPagesUrl = '';

    #[ORM\Column(name: 'keyfiledataversion')]
    private int $keyFileDataVersion = 1;

    #[ORM\Column(name: 'promopaneloverridemode')]
    private int $promoPanelOverrideMode = 0;

    #[ORM\Column(name: 'promopaneloverridestartdate')]
    private \DateTime|null $promoPanelOverrideStartDate = null;

    #[ORM\Column(name: 'promopaneloverrideenddate')]
    private \DateTime|null $promoPanelOverrideEndDate = null;

    #[ORM\Column(name: 'promopaneloverrideurl', length: 100)]
    private string $promoPanelOverrideUrl = '';

    #[ORM\Column(name: 'promopaneloverrideheight')]
    private int $promoPanelOverrideHeight = 0;

    #[ORM\Column(name: 'promopaneloverridepixelratio')]
    private int $promoPanelOverridePixelRatio = 1;

    #[ORM\Column(name: 'promopaneloverridehidpicantoggle')]
    private bool $promoPanelOverrideHiDpiCanToggle = false;

	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @param int|null $id
	 * @return LicenseKey
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
	 * @return LicenseKey
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
	 * @return LicenseKey
	 */
	public function setCompanyCode(string $companyCode): self
	{
		$this->companyCode = $companyCode;
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
	 * @return LicenseKey
	 */
	public function setGroupCode(string $groupCode): self
	{
		$this->groupCode = $groupCode;
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
	 * @return LicenseKey
	 */
	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAddress1(): string
	{
		return $this->address1;
	}

	/**
	 * @param string $address1
	 * @return LicenseKey
	 */
	public function setAddress1(string $address1): self
	{
		$this->address1 = $address1;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAddress2(): string
	{
		return $this->address2;
	}

	/**
	 * @param string $address2
	 * @return LicenseKey
	 */
	public function setAddress2(string $address2): self
	{
		$this->address2 = $address2;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAddress3(): string
	{
		return $this->address3;
	}

	/**
	 * @param string $address3
	 * @return LicenseKey
	 */
	public function setAddress3(string $address3): self
	{
		$this->address3 = $address3;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAddress4(): string
	{
		return $this->address4;
	}

	/**
	 * @param string $address4
	 * @return LicenseKey
	 */
	public function setAddress4(string $address4): self
	{
		$this->address4 = $address4;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCity(): string
	{
		return $this->city;
	}

	/**
	 * @param string $city
	 * @return LicenseKey
	 */
	public function setCity(string $city): self
	{
		$this->city = $city;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCounty(): string
	{
		return $this->county;
	}

	/**
	 * @param string $county
	 * @return LicenseKey
	 */
	public function setCounty(string $county): self
	{
		$this->county = $county;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getState(): string
	{
		return $this->state;
	}

	/**
	 * @param string $state
	 * @return LicenseKey
	 */
	public function setState(string $state): self
	{
		$this->state = $state;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRegionCode(): string
	{
		return $this->regionCode;
	}

	/**
	 * @param string $regionCode
	 * @return LicenseKey
	 */
	public function setRegionCode(string $regionCode): self
	{
		$this->regionCode = $regionCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRegion(): string
	{
		return $this->region;
	}

	/**
	 * @param string $region
	 * @return LicenseKey
	 */
	public function setRegion(string $region): self
	{
		$this->region = $region;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPostCode(): string
	{
		return $this->postCode;
	}

	/**
	 * @param string $postCode
	 * @return LicenseKey
	 */
	public function setPostCode(string $postCode): self
	{
		$this->postCode = $postCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCountryCode(): string
	{
		return $this->countryCode;
	}

	/**
	 * @param string $countryCode
	 * @return LicenseKey
	 */
	public function setCountryCode(string $countryCode): self
	{
		$this->countryCode = $countryCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCountryName(): string
	{
		return $this->countryName;
	}

	/**
	 * @param string $countryName
	 * @return LicenseKey
	 */
	public function setCountryName(string $countryName): self
	{
		$this->countryName = $countryName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTelephoneNumber(): string
	{
		return $this->telephoneNumber;
	}

	/**
	 * @param string $telephoneNumber
	 * @return LicenseKey
	 */
	public function setTelephoneNumber(string $telephoneNumber): self
	{
		$this->telephoneNumber = $telephoneNumber;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmailAddress(): string
	{
		return $this->emailAddress;
	}

	/**
	 * @param string $emailAddress
	 * @return LicenseKey
	 */
	public function setEmailAddress(string $emailAddress): self
	{
		$this->emailAddress = $emailAddress;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getContactFirstName(): string
	{
		return $this->contactFirstName;
	}

	/**
	 * @param string $contactFirstName
	 * @return LicenseKey
	 */
	public function setContactFirstName(string $contactFirstName): self
	{
		$this->contactFirstName = $contactFirstName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getContactLastName(): string
	{
		return $this->contactLastName;
	}

	/**
	 * @param string $contactLastName
	 * @return LicenseKey
	 */
	public function setContactLastName(string $contactLastName): self
	{
		$this->contactLastName = $contactLastName;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isCreateAccounts(): bool
	{
		return $this->createAccounts;
	}

	/**
	 * @param bool $createAccounts
	 * @return LicenseKey
	 */
	public function setCreateAccounts(bool $createAccounts): self
	{
		$this->createAccounts = $createAccounts;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseAddressForBilling(): bool
	{
		return $this->useAddressForBilling;
	}

	/**
	 * @param bool $useAddressForBilling
	 * @return LicenseKey
	 */
	public function setUseAddressForBilling(bool $useAddressForBilling): self
	{
		$this->useAddressForBilling = $useAddressForBilling;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseAddressForShipping(): bool
	{
		return $this->useAddressForShipping;
	}

	/**
	 * @param bool $useAddressForShipping
	 * @return LicenseKey
	 */
	public function setUseAddressForShipping(bool $useAddressForShipping): self
	{
		$this->useAddressForShipping = $useAddressForShipping;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isModifyShippingAddress(): bool
	{
		return $this->modifyShippingAddress;
	}

	/**
	 * @param bool $modifyShippingAddress
	 * @return LicenseKey
	 */
	public function setModifyShippingAddress(bool $modifyShippingAddress): self
	{
		$this->modifyShippingAddress = $modifyShippingAddress;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isModifyBillingAddress(): bool
	{
		return $this->modifyBillingAddress;
	}

	/**
	 * @param bool $modifyBillingAddress
	 * @return LicenseKey
	 */
	public function setModifyBillingAddress(bool $modifyBillingAddress): self
	{
		$this->modifyBillingAddress = $modifyBillingAddress;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isModifyShippingContactDetails(): bool
	{
		return $this->modifyShippingContactDetails;
	}

	/**
	 * @param bool $modifyShippingContactDetails
	 * @return LicenseKey
	 */
	public function setModifyShippingContactDetails(bool $modifyShippingContactDetails): self
	{
		$this->modifyShippingContactDetails = $modifyShippingContactDetails;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUserEmailDestination(): bool
	{
		return $this->userEmailDestination;
	}

	/**
	 * @param bool $userEmailDestination
	 * @return LicenseKey
	 */
	public function setUserEmailDestination(bool $userEmailDestination): self
	{
		$this->userEmailDestination = $userEmailDestination;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getOrderFromPreview(): int
	{
		return $this->orderFromPreview;
	}

	/**
	 * @param int $orderFromPreview
	 * @return LicenseKey
	 */
	public function setOrderFromPreview(int $orderFromPreview): self
	{
		$this->orderFromPreview = $orderFromPreview;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isShowPricesWithTax(): bool
	{
		return $this->showPricesWithTax;
	}

	/**
	 * @param bool $showPricesWithTax
	 * @return LicenseKey
	 */
	public function setShowPricesWithTax(bool $showPricesWithTax): self
	{
		$this->showPricesWithTax = $showPricesWithTax;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isShowTaxBreakDown(): bool
	{
		return $this->showTaxBreakDown;
	}

	/**
	 * @param bool $showTaxBreakDown
	 * @return LicenseKey
	 */
	public function setShowTaxBreakDown(bool $showTaxBreakDown): self
	{
		$this->showTaxBreakDown = $showTaxBreakDown;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isShowZeroTax(): bool
	{
		return $this->showZeroTax;
	}

	/**
	 * @param bool $showZeroTax
	 * @return LicenseKey
	 */
	public function setShowZeroTax(bool $showZeroTax): self
	{
		$this->showZeroTax = $showZeroTax;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isShowAlwaysTaxTotal(): bool
	{
		return $this->showAlwaysTaxTotal;
	}

	/**
	 * @param bool $showAlwaysTaxTotal
	 * @return LicenseKey
	 */
	public function setShowAlwaysTaxTotal(bool $showAlwaysTaxTotal): self
	{
		$this->showAlwaysTaxTotal = $showAlwaysTaxTotal;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLogin(): string
	{
		return $this->login;
	}

	/**
	 * @param string $login
	 * @return LicenseKey
	 */
	public function setLogin(string $login): self
	{
		$this->login = $login;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 * @return LicenseKey
	 */
	public function setPassword(string $password): self
	{
		$this->password = $password;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getKeyFileName(): string
	{
		return $this->keyFileName;
	}

	/**
	 * @param string $keyFileName
	 * @return LicenseKey
	 */
	public function setKeyFileName(string $keyFileName): self
	{
		$this->keyFileName = $keyFileName;
		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getKeyFileNameVersion(): ?DateTime
	{
		return $this->keyFileNameVersion;
	}

	/**
	 * @param DateTime|null $keyFileNameVersion
	 * @return LicenseKey
	 */
	public function setKeyFileNameVersion(?DateTime $keyFileNameVersion): self
	{
		$this->keyFileNameVersion = $keyFileNameVersion;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getKeyFileSize(): int
	{
		return $this->keyFileSize;
	}

	/**
	 * @param int $keyFileSize
	 * @return LicenseKey
	 */
	public function setKeyFileSize(int $keyFileSize): self
	{
		$this->keyFileSize = $keyFileSize;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getKeyFileCheckSum(): string
	{
		return $this->keyFileCheckSum;
	}

	/**
	 * @param string $keyFileCheckSum
	 * @return LicenseKey
	 */
	public function setKeyFileCheckSum(string $keyFileCheckSum): self
	{
		$this->keyFileCheckSum = $keyFileCheckSum;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getKeyUpdatePriority(): int
	{
		return $this->keyUpdatePriority;
	}

	/**
	 * @param int $keyUpdatePriority
	 * @return LicenseKey
	 */
	public function setKeyUpdatePriority(int $keyUpdatePriority): self
	{
		$this->keyUpdatePriority = $keyUpdatePriority;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getWebBrandCode(): string
	{
		return $this->webBrandCode;
	}

	/**
	 * @param string $webBrandCode
	 * @return LicenseKey
	 */
	public function setWebBrandCode(string $webBrandCode): self
	{
		$this->webBrandCode = $webBrandCode;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultCurrency(): bool
	{
		return $this->useDefaultCurrency;
	}

	/**
	 * @param bool $useDefaultCurrency
	 * @return LicenseKey
	 */
	public function setUseDefaultCurrency(bool $useDefaultCurrency): self
	{
		$this->useDefaultCurrency = $useDefaultCurrency;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCurrencyCode(): string
	{
		return $this->currencyCode;
	}

	/**
	 * @param string $currencyCode
	 * @return LicenseKey
	 */
	public function setCurrencyCode(string $currencyCode): self
	{
		$this->currencyCode = $currencyCode;
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
	 * @return LicenseKey
	 */
	public function setTaxCode(string $taxCode): self
	{
		$this->taxCode = $taxCode;
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
	 * @return LicenseKey
	 */
	public function setShippingTaxCode(string $shippingTaxCode): self
	{
		$this->shippingTaxCode = $shippingTaxCode;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRegisteredTaxNumberType(): int
	{
		return $this->registeredTaxNumberType;
	}

	/**
	 * @param int $registeredTaxNumberType
	 * @return LicenseKey
	 */
	public function setRegisteredTaxNumberType(int $registeredTaxNumberType): self
	{
		$this->registeredTaxNumberType = $registeredTaxNumberType;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRegisteredTaxNumber(): string
	{
		return $this->registeredTaxNumber;
	}

	/**
	 * @param string $registeredTaxNumber
	 * @return LicenseKey
	 */
	public function setRegisteredTaxNumber(string $registeredTaxNumber): self
	{
		$this->registeredTaxNumber = $registeredTaxNumber;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultPaymentMethods(): bool
	{
		return $this->useDefaultPaymentMethods;
	}

	/**
	 * @param bool $useDefaultPaymentMethods
	 * @return LicenseKey
	 */
	public function setUseDefaultPaymentMethods(bool $useDefaultPaymentMethods): self
	{
		$this->useDefaultPaymentMethods = $useDefaultPaymentMethods;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPaymentMethods(): string
	{
		return $this->paymentMethods;
	}

	/**
	 * @param string $paymentMethods
	 * @return LicenseKey
	 */
	public function setPaymentMethods(string $paymentMethods): self
	{
		$this->paymentMethods = $paymentMethods;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getDesignerSplashScreenAssetId(): int
	{
		return $this->designerSplashScreenAssetId;
	}

	/**
	 * @param int $designerSplashScreenAssetId
	 * @return LicenseKey
	 */
	public function setDesignerSplashScreenAssetId(int $designerSplashScreenAssetId): self
	{
		$this->designerSplashScreenAssetId = $designerSplashScreenAssetId;
		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getDesignerSplashScreenStartDate(): ?DateTime
	{
		return $this->designerSplashScreenStartDate;
	}

	/**
	 * @param DateTime|null $designerSplashScreenStartDate
	 * @return LicenseKey
	 */
	public function setDesignerSplashScreenStartDate(?DateTime $designerSplashScreenStartDate): self
	{
		$this->designerSplashScreenStartDate = $designerSplashScreenStartDate;
		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getDesignerSplashScreenEndDate(): ?DateTime
	{
		return $this->designerSplashScreenEndDate;
	}

	/**
	 * @param DateTime|null $designerSplashScreenEndDate
	 * @return LicenseKey
	 */
	public function setDesignerSplashScreenEndDate(?DateTime $designerSplashScreenEndDate): self
	{
		$this->designerSplashScreenEndDate = $designerSplashScreenEndDate;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getDesignerBannerAssetId(): int
	{
		return $this->designerBannerAssetId;
	}

	/**
	 * @param int $designerBannerAssetId
	 * @return LicenseKey
	 */
	public function setDesignerBannerAssetId(int $designerBannerAssetId): self
	{
		$this->designerBannerAssetId = $designerBannerAssetId;
		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getDesignerBannerStartDate(): ?DateTime
	{
		return $this->designerBannerStartDate;
	}

	/**
	 * @param DateTime|null $designerBannerStartDate
	 * @return LicenseKey
	 */
	public function setDesignerBannerStartDate(?DateTime $designerBannerStartDate): self
	{
		$this->designerBannerStartDate = $designerBannerStartDate;
		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getDesignerBannerEndDate(): ?DateTime
	{
		return $this->designerBannerEndDate;
	}

	/**
	 * @param DateTime|null $designerBannerEndDate
	 * @return LicenseKey
	 */
	public function setDesignerBannerEndDate(?DateTime $designerBannerEndDate): self
	{
		$this->designerBannerEndDate = $designerBannerEndDate;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getOnlineDesignerGuestWorkflowMode(): int
	{
		return $this->onlineDesignerGuestWorkflowMode;
	}

	/**
	 * @param int $onlineDesignerGuestWorkflowMode
	 * @return LicenseKey
	 */
	public function setOnlineDesignerGuestWorkflowMode(int $onlineDesignerGuestWorkflowMode): self
	{
		$this->onlineDesignerGuestWorkflowMode = $onlineDesignerGuestWorkflowMode;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getMaxOrderBatchSize(): int
	{
		return $this->maxOrderBatchSize;
	}

	/**
	 * @param int $maxOrderBatchSize
	 * @return LicenseKey
	 */
	public function setMaxOrderBatchSize(int $maxOrderBatchSize): self
	{
		$this->maxOrderBatchSize = $maxOrderBatchSize;
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
	 * @return LicenseKey
	 */
	public function setActive(bool $active): self
	{
		$this->active = $active;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAvailableOnline(): bool
	{
		return $this->availableOnline;
	}

	/**
	 * @param bool $availableOnline
	 * @return LicenseKey
	 */
	public function setAvailableOnline(bool $availableOnline): self
	{
		$this->availableOnline = $availableOnline;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCacheVersion(): string
	{
		return $this->cacheVersion;
	}

	/**
	 * @param string $cacheVersion
	 * @return LicenseKey
	 */
	public function setCacheVersion(string $cacheVersion): self
	{
		$this->cacheVersion = $cacheVersion;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getImageScalingBefore(): string
	{
		return $this->imageScalingBefore;
	}

	/**
	 * @param string $imageScalingBefore
	 * @return LicenseKey
	 */
	public function setImageScalingBefore(string $imageScalingBefore): self
	{
		$this->imageScalingBefore = $imageScalingBefore;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isImageScalingBeforeEnabled(): bool
	{
		return $this->imageScalingBeforeEnabled;
	}

	/**
	 * @param bool $imageScalingBeforeEnabled
	 * @return LicenseKey
	 */
	public function setImageScalingBeforeEnabled(bool $imageScalingBeforeEnabled): self
	{
		$this->imageScalingBeforeEnabled = $imageScalingBeforeEnabled;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultImageScalingBefore(): bool
	{
		return $this->useDefaultImageScalingBefore;
	}

	/**
	 * @param bool $useDefaultImageScalingBefore
	 * @return LicenseKey
	 */
	public function setUseDefaultImageScalingBefore(bool $useDefaultImageScalingBefore): self
	{
		$this->useDefaultImageScalingBefore = $useDefaultImageScalingBefore;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getImageScalingAfter(): string
	{
		return $this->imageScalingAfter;
	}

	/**
	 * @param string $imageScalingAfter
	 * @return LicenseKey
	 */
	public function setImageScalingAfter(string $imageScalingAfter): self
	{
		$this->imageScalingAfter = $imageScalingAfter;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isImageScalingAfterEnabled(): bool
	{
		return $this->imageScalingAfterEnabled;
	}

	/**
	 * @param bool $imageScalingAfterEnabled
	 * @return LicenseKey
	 */
	public function setImageScalingAfterEnabled(bool $imageScalingAfterEnabled): self
	{
		$this->imageScalingAfterEnabled = $imageScalingAfterEnabled;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultImageScalingAfter(): bool
	{
		return $this->useDefaultImageScalingAfter;
	}

	/**
	 * @param bool $useDefaultImageScalingAfter
	 * @return LicenseKey
	 */
	public function setUseDefaultImageScalingAfter(bool $useDefaultImageScalingAfter): self
	{
		$this->useDefaultImageScalingAfter = $useDefaultImageScalingAfter;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getShuffleLayout(): int
	{
		return $this->shuffleLayout;
	}

	/**
	 * @param int $shuffleLayout
	 * @return LicenseKey
	 */
	public function setShuffleLayout(int $shuffleLayout): self
	{
		$this->shuffleLayout = $shuffleLayout;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isShowShuffleLayoutOptions(): bool
	{
		return $this->showShuffleLayoutOptions;
	}

	/**
	 * @param bool $showShuffleLayoutOptions
	 * @return LicenseKey
	 */
	public function setShowShuffleLayoutOptions(bool $showShuffleLayoutOptions): self
	{
		$this->showShuffleLayoutOptions = $showShuffleLayoutOptions;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultShuffleLayout(): bool
	{
		return $this->useDefaultShuffleLayout;
	}

	/**
	 * @param bool $useDefaultShuffleLayout
	 * @return LicenseKey
	 */
	public function setUseDefaultShuffleLayout(bool $useDefaultShuffleLayout): self
	{
		$this->useDefaultShuffleLayout = $useDefaultShuffleLayout;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getOnlineEditorMode(): int
	{
		return $this->onlineEditorMode;
	}

	/**
	 * @param int $onlineEditorMode
	 * @return LicenseKey
	 */
	public function setOnlineEditorMode(int $onlineEditorMode): self
	{
		$this->onlineEditorMode = $onlineEditorMode;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isEnableSwitchingEditor(): bool
	{
		return $this->enableSwitchingEditor;
	}

	/**
	 * @param bool $enableSwitchingEditor
	 * @return LicenseKey
	 */
	public function setEnableSwitchingEditor(bool $enableSwitchingEditor): self
	{
		$this->enableSwitchingEditor = $enableSwitchingEditor;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultOnlineEditorMode(): bool
	{
		return $this->useDefaultOnlineEditorMode;
	}

	/**
	 * @param bool $useDefaultOnlineEditorMode
	 * @return LicenseKey
	 */
	public function setUseDefaultOnlineEditorMode(bool $useDefaultOnlineEditorMode): self
	{
		$this->useDefaultOnlineEditorMode = $useDefaultOnlineEditorMode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOnlineDesignerLogoLinkUrl(): string
	{
		return $this->onlineDesignerLogoLinkUrl;
	}

	/**
	 * @param string $onlineDesignerLogoLinkUrl
	 * @return LicenseKey
	 */
	public function setOnlineDesignerLogoLinkUrl(string $onlineDesignerLogoLinkUrl): self
	{
		$this->onlineDesignerLogoLinkUrl = $onlineDesignerLogoLinkUrl;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultOnlineDesignerLogoLinkUrl(): bool
	{
		return $this->useDefaultOnlineDesignerLogoLinkUrl;
	}

	/**
	 * @param bool $useDefaultOnlineDesignerLogoLinkUrl
	 * @return LicenseKey
	 */
	public function setUseDefaultOnlineDesignerLogoLinkUrl(bool $useDefaultOnlineDesignerLogoLinkUrl): self
	{
		$this->useDefaultOnlineDesignerLogoLinkUrl = $useDefaultOnlineDesignerLogoLinkUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOnlineDesignerLogoLinkTooltip(): string
	{
		return $this->onlineDesignerLogoLinkTooltip;
	}

	/**
	 * @param string $onlineDesignerLogoLinkTooltip
	 * @return LicenseKey
	 */
	public function setOnlineDesignerLogoLinkTooltip(string $onlineDesignerLogoLinkTooltip): self
	{
		$this->onlineDesignerLogoLinkTooltip = $onlineDesignerLogoLinkTooltip;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultVoucherSettings(): bool
	{
		return $this->useDefaultVoucherSettings;
	}

	/**
	 * @param bool $useDefaultVoucherSettings
	 * @return LicenseKey
	 */
	public function setUseDefaultVoucherSettings(bool $useDefaultVoucherSettings): self
	{
		$this->useDefaultVoucherSettings = $useDefaultVoucherSettings;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAllowVouchers(): bool
	{
		return $this->allowVouchers;
	}

	/**
	 * @param bool $allowVouchers
	 * @return LicenseKey
	 */
	public function setAllowVouchers(bool $allowVouchers): self
	{
		$this->allowVouchers = $allowVouchers;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAllowGiftCards(): bool
	{
		return $this->allowGiftCards;
	}

	/**
	 * @param bool $allowGiftCards
	 * @return LicenseKey
	 */
	public function setAllowGiftCards(bool $allowGiftCards): self
	{
		$this->allowGiftCards = $allowGiftCards;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultSizeAndPositionSettings(): bool
	{
		return $this->useDefaultSizeAndPositionSettings;
	}

	/**
	 * @param bool $useDefaultSizeAndPositionSettings
	 * @return LicenseKey
	 */
	public function setUseDefaultSizeAndPositionSettings(bool $useDefaultSizeAndPositionSettings): self
	{
		$this->useDefaultSizeAndPositionSettings = $useDefaultSizeAndPositionSettings;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSizeAndPositionMeasurementUnits(): int
	{
		return $this->sizeAndPositionMeasurementUnits;
	}

	/**
	 * @param int $sizeAndPositionMeasurementUnits
	 * @return LicenseKey
	 */
	public function setSizeAndPositionMeasurementUnits(int $sizeAndPositionMeasurementUnits): self
	{
		$this->sizeAndPositionMeasurementUnits = $sizeAndPositionMeasurementUnits;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSmartGuidesEnable(): bool
	{
		return $this->smartGuidesEnable;
	}

	/**
	 * @param bool $smartGuidesEnable
	 * @return LicenseKey
	 */
	public function setSmartGuidesEnable(bool $smartGuidesEnable): self
	{
		$this->smartGuidesEnable = $smartGuidesEnable;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmartGuidesObjectGuideColour(): string
	{
		return $this->smartGuidesObjectGuideColour;
	}

	/**
	 * @param string $smartGuidesObjectGuideColour
	 * @return LicenseKey
	 */
	public function setSmartGuidesObjectGuideColour(string $smartGuidesObjectGuideColour): self
	{
		$this->smartGuidesObjectGuideColour = $smartGuidesObjectGuideColour;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmartGuidesPageGuideColour(): string
	{
		return $this->smartGuidesPageGuideColour;
	}

	/**
	 * @param string $smartGuidesPageGuideColour
	 * @return LicenseKey
	 */
	public function setSmartGuidesPageGuideColour(string $smartGuidesPageGuideColour): self
	{
		$this->smartGuidesPageGuideColour = $smartGuidesPageGuideColour;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultSmartGuidesSettings(): bool
	{
		return $this->useDefaultSmartGuidesSettings;
	}

	/**
	 * @param bool $useDefaultSmartGuidesSettings
	 * @return LicenseKey
	 */
	public function setUseDefaultSmartGuidesSettings(bool $useDefaultSmartGuidesSettings): self
	{
		$this->useDefaultSmartGuidesSettings = $useDefaultSmartGuidesSettings;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultAutomaticallyApplyPerfectlyClear(): bool
	{
		return $this->useDefaultAutomaticallyApplyPerfectlyClear;
	}

	/**
	 * @param bool $useDefaultAutomaticallyApplyPerfectlyClear
	 * @return LicenseKey
	 */
	public function setUseDefaultAutomaticallyApplyPerfectlyClear(bool $useDefaultAutomaticallyApplyPerfectlyClear): self
	{
		$this->useDefaultAutomaticallyApplyPerfectlyClear = $useDefaultAutomaticallyApplyPerfectlyClear;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAutomaticallyApplyPerfectlyClear(): bool
	{
		return $this->automaticallyApplyPerfectlyClear;
	}

	/**
	 * @param bool $automaticallyApplyPerfectlyClear
	 * @return LicenseKey
	 */
	public function setAutomaticallyApplyPerfectlyClear(bool $automaticallyApplyPerfectlyClear): self
	{
		$this->automaticallyApplyPerfectlyClear = $automaticallyApplyPerfectlyClear;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAllowUsersToTogglePerfectlyClear(): bool
	{
		return $this->allowUsersToTogglePerfectlyClear;
	}

	/**
	 * @param bool $allowUsersToTogglePerfectlyClear
	 * @return LicenseKey
	 */
	public function setAllowUsersToTogglePerfectlyClear(bool $allowUsersToTogglePerfectlyClear): self
	{
		$this->allowUsersToTogglePerfectlyClear = $allowUsersToTogglePerfectlyClear;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultInsertDeleteButtonsVisibility(): bool
	{
		return $this->useDefaultInsertDeleteButtonsVisibility;
	}

	/**
	 * @param bool $useDefaultInsertDeleteButtonsVisibility
	 * @return LicenseKey
	 */
	public function setUseDefaultInsertDeleteButtonsVisibility(bool $useDefaultInsertDeleteButtonsVisibility): self
	{
		$this->useDefaultInsertDeleteButtonsVisibility = $useDefaultInsertDeleteButtonsVisibility;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isInsertDeleteButtonsVisibility(): bool
	{
		return $this->insertDeleteButtonsVisibility;
	}

	/**
	 * @param bool $insertDeleteButtonsVisibility
	 * @return LicenseKey
	 */
	public function setInsertDeleteButtonsVisibility(bool $insertDeleteButtonsVisibility): self
	{
		$this->insertDeleteButtonsVisibility = $insertDeleteButtonsVisibility;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultTotalPagesDropDownMode(): bool
	{
		return $this->useDefaultTotalPagesDropDownMode;
	}

	/**
	 * @param bool $useDefaultTotalPagesDropDownMode
	 * @return LicenseKey
	 */
	public function setUseDefaultTotalPagesDropDownMode(bool $useDefaultTotalPagesDropDownMode): self
	{
		$this->useDefaultTotalPagesDropDownMode = $useDefaultTotalPagesDropDownMode;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getTotalPagesDropDownMode(): int
	{
		return $this->totalPagesDropDownMode;
	}

	/**
	 * @param int $totalPagesDropDownMode
	 * @return LicenseKey
	 */
	public function setTotalPagesDropDownMode(int $totalPagesDropDownMode): self
	{
		$this->totalPagesDropDownMode = $totalPagesDropDownMode;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultAveragePicturesPerPage(): bool
	{
		return $this->useDefaultAveragePicturesPerPage;
	}

	/**
	 * @param bool $useDefaultAveragePicturesPerPage
	 * @return LicenseKey
	 */
	public function setUseDefaultAveragePicturesPerPage(bool $useDefaultAveragePicturesPerPage): LicenseKey
	{
		$this->useDefaultAveragePicturesPerPage = $useDefaultAveragePicturesPerPage;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getAveragePicturesPerPage(): int
	{
		return $this->averagePicturesPerPage;
	}

	/**
	 * @param int $averagePicturesPerPage
	 * @return LicenseKey
	 */
	public function setAveragePicturesPerPage(int $averagePicturesPerPage): LicenseKey
	{
		$this->averagePicturesPerPage = $averagePicturesPerPage;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultComponentUpsellSettings(): bool
	{
		return $this->useDefaultComponentUpsellSettings;
	}

	/**
	 * @param bool $useDefaultComponentUpsellSettings
	 * @return LicenseKey
	 */
	public function setUseDefaultComponentUpsellSettings(bool $useDefaultComponentUpsellSettings): LicenseKey
	{
		$this->useDefaultComponentUpsellSettings = $useDefaultComponentUpsellSettings;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getComponentUpsellSettings(): int
	{
		return $this->componentUpsellSettings;
	}

	/**
	 * @param int $componentUpsellSettings
	 * @return LicenseKey
	 */
	public function setComponentUpsellSettings(int $componentUpsellSettings): LicenseKey
	{
		$this->componentUpsellSettings = $componentUpsellSettings;
		return $this;
	}

    /**
     * @return bool
     */
    public function isUseDefaultAccountPagesUrl(): bool
    {
        return $this->useDefaultAccountPagesUrl;
    }

    /**
     * @param bool $useDefaultAccountPagesUrl
     * @return LicenseKey
     */
    public function setUseDefaultAccountPagesUrl(bool $useDefaultAccountPagesUrl): LicenseKey
    {
        $this->useDefaultAccountPagesUrl = $useDefaultAccountPagesUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccountPagesUrl(): string
    {
        return $this->accountPagesUrl;
    }

    /**
     * @param string $accountPagesUrl
     * @return LicenseKey
     */
    public function setAccountPagesUrl(string $accountPagesUrl): LicenseKey
    {
        $this->accountPagesUrl = $accountPagesUrl;
        return $this;
    }

    /**
     * @return int
     */
    public function getKeyFileDataVersion(): int
    {
        return $this->keyFileDataVersion;
    }

    /**
     * @param int $keyFileDataVersion
     * @return LicenseKey
     */
    public function setKeyFileDataVersion(int $keyFileDataVersion): LicenseKey
    {
        $this->keyFileDataVersion = $keyFileDataVersion;
        return $this;
    }

    /**
     * @return int
     */
    public function getPromoPanelOverrideMode(): int
    {
        return $this->promoPanelOverrideMode;
    }

    /**
     * @param int $promoPanelOverrideMode
     * @return LicenseKey
     */
    public function setPromoPanelOverrideMode(int $promoPanelOverrideMode): LicenseKey
    {
        $this->promoPanelOverrideMode = $promoPanelOverrideMode;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getPromoPanelOverrideStartDate(): ?DateTime
    {
        return $this->promoPanelOverrideStartDate;
    }

    /**
     * @param DateTime|null $promoPanelOverrideStartDate
     * @return LicenseKey
     */
    public function setPromoPanelOverrideStartDate(?DateTime $promoPanelOverrideStartDate): LicenseKey
    {
        $this->promoPanelOverrideStartDate = $promoPanelOverrideStartDate;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getPromoPanelOverrideEndDate(): ?DateTime
    {
        return $this->promoPanelOverrideEndDate;
    }

    /**
     * @param DateTime|null $promoPanelOverrideEndDate
     * @return LicenseKey
     */
    public function setPromoPanelOverrideEndDate(?DateTime $promoPanelOverrideEndDate): LicenseKey
    {
        $this->promoPanelOverrideEndDate = $promoPanelOverrideEndDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getPromoPanelOverrideUrl(): string
    {
        return $this->promoPanelOverrideUrl;
    }

    /**
     * @param string $promoPanelOverrideUrl
     * @return LicenseKey
     */
    public function setPromoPanelOverrideUrl(string $promoPanelOverrideUrl): LicenseKey
    {
        $this->promoPanelOverrideUrl = $promoPanelOverrideUrl;
        return $this;
    }

    /**
     * @return int
     */
    public function getPromoPanelOverrideHeight(): int
    {
        return $this->promoPanelOverrideHeight;
    }

    /**
     * @param int $promoPanelOverrideHeight
     * @return LicenseKey
     */
    public function setPromoPanelOverrideHeight(int $promoPanelOverrideHeight): LicenseKey
    {
        $this->promoPanelOverrideHeight = $promoPanelOverrideHeight;
        return $this;
    }

    /**
     * @return int
     */
    public function getPromoPanelOverridePixelRatio(): int
    {
        return $this->promoPanelOverridePixelRatio;
    }

    /**
     * @param int $promoPanelOverridePixelRatio
     * @return LicenseKey
     */
    public function setPromoPanelOverridePixelRatio(int $promoPanelOverridePixelRatio): LicenseKey
    {
        $this->promoPanelOverridePixelRatio = $promoPanelOverridePixelRatio;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPromoPanelOverrideHiDpiCanToggle(): bool
    {
        return $this->promoPanelOverrideHiDpiCanToggle;
    }

    /**
     * @param bool $promoPanelOverrideHiDpiCanToggle
     * @return LicenseKey
     */
    public function setPromoPanelOverrideHiDpiCanToggle(bool $promoPanelOverrideHiDpiCanToggle): LicenseKey
    {
        $this->promoPanelOverrideHiDpiCanToggle = $promoPanelOverrideHiDpiCanToggle;
        return $this;
    }
}
