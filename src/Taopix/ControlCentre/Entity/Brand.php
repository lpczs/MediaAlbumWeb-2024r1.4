<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedPath;
use Taopix\ControlCentre\Repository\BrandRepository;
use Taopix\ControlCentre\Traits\Entity\ToArrayTrait;

#[ORM\Entity(repositoryClass: BrandRepository::class), ORM\Table(name: "branding", schema: "controlcentre"), ORM\UniqueConstraint(name: "code", columns: ["code"])]
class Brand
{
	use ToArrayTrait;

	#[ORM\Id, ORM\GeneratedValue, ORM\Column(name: "id", nullable: false)]
	private ?int $id = null;

	#[ORM\Column(name: "datecreated", nullable: false)]
	private ?DateTime $dateCreated = null;

	#[ORM\Column(name: "datelastmodified", nullable: false), Groups(['brand-details'])]
	private ?DateTime $dateLastModified = null;

	#[ORM\Column(name: "companycode", length: 50, nullable: false)]
	private string $companyCode = '';

	#[ORM\Column(name: "owner", length: 50, nullable: false)]
	private string $owner = '';

	#[ORM\Column(name: "code", length: 50, nullable: false), Groups(['brand-details'])]
	private string $code = '';

	#[ORM\Column(name: "name", length: 50, nullable: false)]
	private string $name = '';

	#[ORM\Column(name: "applicationname", length: 50, nullable: false), Groups(['brand-details']), SerializedPath('[name]')]
	private string $applicationName = '';

	#[ORM\Column(name: "displayurl", length: 100, nullable: false)]
	private string $displayUrl = '';

	#[ORM\Column(name: "weburl", length: 100, nullable: false), SerializedPath('[urls][callback]'), Groups(['brand-details'])]
	private string $webUrl = '';

	#[ORM\Column(name: "onlinedesignerurl", length: 100, nullable: false), SerializedPath('[urls][designer]'), Groups(['brand-details'])]
	private string $onlineDesignerUrl = '';

	#[ORM\Column(name: "onlinedesignerlogouturl", length: 100, nullable: false), SerializedPath('[urls][logout]'), Groups(['brand-details'])]
	private string $onlineDesignerLogoutUrl = '';

	#[ORM\Column(name: "mainwebsiteurl", length: 100, nullable: false), SerializedPath('[urls][mainSite]'), Groups(['brand-details'])]
	private string $mainWebsiteUrl = '';

	#[ORM\Column(name: "macdownloadurl", length: 100, nullable: false)]
	private string $macDownloadUrl = '';

	#[ORM\Column(name: "win32downloadurl", length: 100, nullable: false)]
	private string $win32DownloadUrl = '';

	#[ORM\Column(name: "supporttelephonenumber", length: 50, nullable: false)]
	private string $supportTelephoneNumber = '';

	#[ORM\Column(name: "supportemailaddress", length: 50, nullable: false)]
	private string $supportEmailAddress = '';

	#[ORM\Column(name: "defaultcommunicationpreference", nullable: false)]
	private int $defaultCommunicationPreference = 1;

	#[ORM\Column(name: "registerusingemail", nullable: false), SerializedPath('[general][emailIsUserName]'), Groups(['brand-details'])]
	private bool $registerUsingEmail = true;

	#[ORM\Column(name: "sharebyemailmethod", nullable: false), SerializedPath('[share][shareByEmailMathod]'), Groups(['brand-details'])]
	private int $shareByEmailMethod = 1;

	#[ORM\Column(name: "orderfrompreview", nullable: false), SerializedPath('[share][orderFromPreview]'), Groups(['brand-details'])]
	private bool $orderFromPreview = true;

	#[ORM\Column(name: "sharehidebranding", nullable: false)]
	private bool $shareHideBranding = false;

	#[ORM\Column(name: "previewdomainurl", length: 100, nullable: false)]
	private string $previewDomainUrl = '';

	#[ORM\Column(name: "usedefaultpaymentmethods", nullable: false)]
	private bool $useDefaultPaymentMethods = true;

	#[ORM\Column(name: "paymentmethods", length: 100, nullable: false)]
	private string $paymentMethods = '';

	#[ORM\Column(name: "paymentintegration", length: 20, nullable: false)]
	private string $paymentIntegration = 'DEFAULT';

	#[ORM\Column(name: "allowgiftcards", nullable: false)]
	private bool $allowGiftCards = true;

	#[ORM\Column(name: "allowvouchers", nullable: false)]
	private bool $allowVouchers = true;

	#[ORM\Column(name: "usedefaultemailsettings", nullable: false)]
	private bool $useDefaultEmailSettings = true;

	#[ORM\Column(name: "smtpaddress", length: 100, nullable: false)]
	private string $smtpAddress = '';

	#[ORM\Column(name: "smtpport", nullable: false)]
	private int $smtpPort = 25;

	#[ORM\Column(name: "smtpauth", nullable: false)]
	private bool $smtpAuth = false;

	#[ORM\Column(name: "smtpauthusername", length: 50, nullable: false)]
	private string $smtpAuthUserName = '';

	#[ORM\Column(name: "smtpauthpassword", length: 512, nullable: false)]
	private string $smtpAuthPassword = '';

	#[ORM\Column(name: "smtptype", length: 50, nullable: false)]
	private string $smtpType = '';

	#[ORM\Column(name: "smtpsystemfromname", length: 200, nullable: false)]
	private string $smtpSystemFromName = '';

	#[ORM\Column(name: "smtpsystemfromaddress", length: 200, nullable: false)]
	private string $smtpSystemFromAddress = '';

	#[ORM\Column(name: "smtpsystemreplytoname", length: 200, nullable: false)]
	private string $smtpSystemReplyToName = '';

	#[ORM\Column(name: "smtpsystemreplytoaddress", length: 200, nullable: false)]
	private string $smtpSystemReplyToAddress = '';

	#[ORM\Column(name: "smtpadminname", length: 200, nullable: false)]
	private string $smtpAdminName = '';

	#[ORM\Column(name: "smtpadminaddress", length: 200, nullable: false)]
	private string $smtpAdminAddress = '';

	#[ORM\Column(name: "smtpadminactive", nullable: false)]
	private int $smtpAdminActive = 1;

	#[ORM\Column(name: "smtpproductionname", length: 200, nullable: false)]
	private string $smtpProductionName = '';

	#[ORM\Column(name: "smtpproductionaddress", length: 200, nullable: false)]
	private string $smtpProductionAddress = '';

	#[ORM\Column(name: "smtpproductionactive", nullable: false)]
	private int $smtpProductionActive = 1;

	#[ORM\Column(name: "smtporderconfirmationname", length: 200, nullable: false)]
	private string $smtpOrderConfirmationName = '';

	#[ORM\Column(name: "smtporderconfirmationaddress", length: 200, nullable: false)]
	private string $smtpOrderConfirmationAddress = '';

	#[ORM\Column(name: "smtporderconfirmationactive", nullable: false)]
	private int $smtpOrderConfirmationActive = 1;

	#[ORM\Column(name: "smtpsaveordername", length: 200, nullable: false)]
	private string $smtpSaveOrderName = '';

	#[ORM\Column(name: "smtpsaveorderaddress", length: 200, nullable: false)]
	private string $smtpSaveOrderAddress = '';

	#[ORM\Column(name: "smtpsaveorderactive", nullable: false)]
	private int $smtpSaveOrderActive = 1;

	#[ORM\Column(name: "smtpshippingname", length: 200, nullable: false)]
	private string $smtpShippingName = '';

	#[ORM\Column(name: "smtpshippingaddress", length: 200, nullable: false)]
	private string $smtpShippingAddress = '';

	#[ORM\Column(name: "smtpshippingactive", nullable: false)]
	private int $smtpShippingactive = 1;

	#[ORM\Column(name: "smtpnewaccountname", length: 200, nullable: false)]
	private string $smtpNewAccountName = '';

	#[ORM\Column(name: "smtpnewaccountaddress", length: 200, nullable: false)]
	private string $smtpNewAccountAddress = '';

	#[ORM\Column(name: "smtpnewaccountactive", nullable: false)]
	private int $smtpNewAccountActive = 1;

	#[ORM\Column(name: "smtpresetpasswordname", length: 200, nullable: false)]
	private string $smtpResetPasswordName = '';

	#[ORM\Column(name: "smtpresetpasswordaddress", length: 200, nullable: false)]
	private string $smtpResetPasswordAddress = '';

	#[ORM\Column(name: "smtpresetpasswordactive", nullable: false)]
	private int $smtpResetPasswordActive = 1;

	#[ORM\Column(name: "smtporderuploadedname", length: 200, nullable: false)]
	private string $smtpOrderUploadedName = '';

	#[ORM\Column(name: "smtporderuploadedaddress", length: 200, nullable: false)]
	private string $smtpOrderUploadedAddress = '';

	#[ORM\Column(name: "smtporderuploadedactive", nullable: false)]
	private int $smtpOrderUploadedActive = 0;

	#[ORM\Column(name: "previewlicensekey", length: 1024, nullable: false)]
	private string $previewLicenseKey = '';

	#[ORM\Column(name: "previewexpires", nullable: false)]
	private int $previewExpires = 0;

	#[ORM\Column(name: "previewexpiresdays", nullable: false)]
	private int $previewExpiresDays = 0;

	#[ORM\Column(name: "onlinedesignersigninregisterpromptdelay", nullable: false), SerializedPath('[general][signinDelay]'), Groups(['brand-details'])]
	private int $onlineDesignerSigninRegisterPromptDelay = 10;

	#[ORM\Column(name: "onlinedataretentionpolicy", nullable: false)]
	private int $onlineDataRetentionPolicy = 0;

	#[ORM\Column(name: "onlinedesignerusemultilineworkflow", nullable: false), SerializedPath('[general][multiline]'), Groups(['brand-details'])]
	private bool $onlineDesignerUseMultilineWorkflow = false;

	#[ORM\Column(name: "googleanalyticscode", length: 20, nullable: false)]
	private string $googleAnalyticsCode = '';

	#[ORM\Column(name: "googleanalyticsuseridtracking", nullable: false)]
	private bool $googleAnalyticsUserIdTracking = false;

	#[ORM\Column(name: "googletagmanageronlinecode", length: 20, nullable: false), SerializedPath('[general][tagManager][id]'), Groups(['brand-details'])]
	private string $googleTagManagerOnlineCode = '';

	#[ORM\Column(name: "googletagmanagercccode", length: 20, nullable: false)]
	private string $googleTagManagerCCode = '';

	#[ORM\Column(name: "productcategoryassetid", nullable: false)]
	private int $productCategoryAssetId = 0;

	#[ORM\Column(name: "productcategoryassetversion", length: 50, nullable: false)]
	private ?DateTime $productCategoryAssetVersion = null;

	#[ORM\Column(name: "calendardataassetid", nullable: false)]
	private int $calendarDataAssetId = 0;

	#[ORM\Column(name: "calendardataassetversion", nullable: false)]
	private ?DateTime $calendarDataAssetVersion = null;

	#[ORM\Column(name: "redactionmode", type: Types::SMALLINT, nullable: false)]
	private int $redactionMode = 0;

	#[ORM\Column(name: "automaticredactionenabled", nullable: false)]
	private bool $automaticRedactionEnabled = false;

	#[ORM\Column(name: "automaticredactiondays", nullable: false)]
	private int $automaticRedactionDays = 365;

	#[ORM\Column(name: "redactionnotificationdays", nullable: false)]
	private int $redactionNotificationDays = 7;

	#[ORM\Column(name: "orderredactiondays", nullable: false)]
	private int $orderRedactionDays = 0;

	#[ORM\Column(name: "orderredactionmode", type: Types::SMALLINT, nullable: false)]
	private int $orderRedactionMode = 0;

	#[ORM\Column(name: "aucacheversionmasks", length: 30, nullable: false)]
	private string $auCacheVersionMasks = '';

	#[ORM\Column(name: "aucacheversionbackgrounds", length: 30, nullable: false)]
	private string $auCacheVersionBackgrounds = '';

	#[ORM\Column(name: "aucacheversionscrapbook", length: 30, nullable: false)]
	private string $auCacheVersionScrapbook = '';

	#[ORM\Column(name: "aucacheversionframes", length: 30, nullable: false)]
	private string $auCacheVersionFrames = '';

	#[ORM\Column(name: "imagescalingbefore", type: Types::DECIMAL, precision: 5, scale: 2, nullable: false, options: ["default" => "0.00"]), SerializedPath('[imageScaling][before][maxMp]')]
	#[Groups(['brand-details'])]
    private string $imageScalingBefore = '0.00';

	#[ORM\Column(name: "imagescalingbeforeenabled", nullable: false), SerializedPath('[imageScaling][before][enabled]'), Groups(['brand-details'])]
	private bool $imageScalingBeforeEnabled = false;

	#[ORM\Column(name: "imagescalingafter", type: Types::DECIMAL, precision: 5, scale: 2, nullable: false, options: ["default" => "36.00"]), SerializedPath('[imageScaling][after][maxMp]')]
	#[Groups(['brand-details'])]
    private string $imageScalingAfter = '36.00';

	#[ORM\Column(name: "imagescalingafterenabled", nullable: false), SerializedPath('[imageScaling][after][enabled]'), Groups(['brand-details'])]
	private bool $imageScalingAfterEnabled = true;

	#[ORM\Column(name: "shufflelayout", type: Types::SMALLINT, nullable: false), SerializedPath('[shuffle][options]'), Groups(['brand-details'])]
	private int $shuffleLayout = 0;

	#[ORM\Column(name: "showshufflelayoutoptions", nullable: false), SerializedPath('[shuffle][enabled]'), Groups(['brand-details'])]
	private bool $showShuffleLayoutOptions = false;

	#[ORM\Column(name: "onlineeditormode", type: Types::SMALLINT, nullable: false), SerializedPath('[editor][default]'), Groups(['brand-details'])]
	private int $onlineEditorMode = 0;

	#[ORM\Column(name: "enableswitchingeditor", nullable: false), SerializedPath('[editor][switchable]'), Groups(['brand-details'])]
	private bool $enableSwitchingEditor = true;

	#[ORM\Column(name: "onlinedesignerlogolinkurl", length: 100, nullable: false), SerializedPath('[logo][url]'), Groups(['brand-details'])]
	private string $onlineDesignerLogoLinkUrl = '';

	#[ORM\Column(name: "onlinedesignerlogolinktooltip", length: 10240, nullable: false), SerializedPath('[logo][toolTip]'), Groups(['brand-details'])]
	private string $onlineDesignerLogoLinkTooltip = '';

	#[ORM\Column(name: "sizeandpositionmeasurementunits", type: Types::SMALLINT, nullable: false), SerializedPath('[general][units]'), Groups(['brand-details'])]
	private int $sizeAndPositionMeasurementUnits = 0;

	#[ORM\Column(name: "smartguidesenable", nullable: false), SerializedPath('[smartGuides][enabled]'), Groups(['brand-details'])]
	private bool $smartGuidesEnable = true;

	#[ORM\Column(name: "smartguidesobjectguidecolour", length: 60, nullable: false, options: ["default" => "00CCFF"]), SerializedPath('[smartGuides][colours][object]')]
    #[Groups(['brand-details'])]
	private string $smartGuidesObjectGuideColour = '00CCFF';

	#[ORM\Column(name: "smartguidespageguidecolour", length: 6, nullable: false, options: ["default" => "FF00FF"]), SerializedPath('[smartGuides][colours][page]')]
    #[Groups(['brand-details'])]
	private string $smartGuidesPageGuideColour = 'FF00FF';

	#[ORM\Column(name: "automaticallyapplyperfectlyclear", nullable: false), SerializedPath('[autoEnhance][mode]'), Groups(['brand-details'])]
	private bool $automaticallyApplyPerfectlyClear = false;

	#[ORM\Column(name: "allowuserstotoggleperfectlyclear", nullable: false), SerializedPath('[autoEnhance][canToggle]'), Groups(['brand-details'])]
	private bool $allowUsersToTogglePerfectlyClear = false;

	#[ORM\Column(name: "onlinedesignercdnurl", length: 100, nullable: false), SerializedPath('[urls][cdn]'), Groups(['brand-details'])]
	private string $onlineDesignerCDNUrl = '';

	#[ORM\Column(name: "insertdeletebuttonsvisibility", nullable: false), SerializedPath('[pages][insertAndDelete]'), Groups(['brand-details'])]
	private bool $insertDeleteButtonsVisibility = true;

	#[ORM\Column(name: "totalpagesdropdownmode", type: Types::SMALLINT, nullable: false), SerializedPath('[pages][totalPagesDropDown]'), Groups(['brand-details'])]
	private int $totalPagesDropDownMode = 1;

	#[ORM\Column(name: "averagepicturesperpage", nullable: false, options: ["unsigned" => true]), SerializedPath('[pages][averagePictures]'), Groups(['brand-details'])]
	private int $averagePicturesPerPage = 0;

	#[ORM\Column(name: "componentupsellsettings", nullable: false), SerializedPath('[general][componentUpsell]'), Groups(['brand-details'])]
	private int $componentUpsellSettings = 3;

	#[ORM\Column(name: "active", nullable: false)]
	private bool $active = false;

    #[ORM\Column(name: 'oauthprovider', type: Types::BIGINT, options: ['unsigned' => true, 'default' => 0])]
    private string $oAuthProvider = '0';

    #[ORM\Column(name: 'oauthtoken', type: Types::BIGINT, options: ['unsigned' => true, 'default' => 0])]
    private string $oAuthToken = '0';

    #[ORM\Column(name: 'usedefaultaccountpagesurl')]
    private bool $useDefaultAccountPagesUrl = true;

    #[ORM\Column(name: 'accountpagesurl', length: 100)]
    private string $accountPagesUrl = '';

    #[ORM\Column(name: 'onlineuiurl', length: 200, nullable: false), SerializedPath('[urls][ui]'), Groups(['brand-details'])]
    private string $onlineUiUrl = '';

    #[ORM\Column(name: 'onlineapiurl', length: 200, nullable: false), SerializedPath('[urls][api]'), Groups(['brand-details'])]
    private string $onlineApiUrl = '';

    #[ORM\Column(name: 'onlineappkeyentropyvalue', length: 64, nullable: false), SerializedPath('[misc][ev]'), Groups(['brand-details'])]
    private string $onlineAppKeyEntropyValue = '';

    #[ORM\Column(name: 'onlineabouturl', length: 200, nullable: false), SerializedPath('[urls][about]'), Groups(['brand-details'])]
    private string $onlineAboutUrl = '';

    #[ORM\Column(name: 'onlinehelpurl', length: 200, nullable: false), SerializedPath('[urls][help]'), Groups(['brand-details'])]
    private string $onlineHelpUrl = '';

    #[ORM\Column(name: 'onlinetermsandconditionsurl', length: 200, nullable: false), SerializedPath('[urls][terms]'), Groups(['brand-details'])]
    private string $onlineTermsAndConditionsUrl = '';

    #[ORM\Column(name: 'theme', type: Types::INTEGER, options: ['unsigned' => true, 'default' => 0]), SerializedPath('[general][theme]'), Groups(['brand-details'])]
    private int $theme;

	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @param int|null $id
	 * @return Brand
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
	 * @return Brand
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
	 * @return Brand
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
	 * @return Brand
	 */
	public function setCompanyCode(string $companyCode): self
	{
		$this->companyCode = $companyCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOwner(): string
	{
		return $this->owner;
	}

	/**
	 * @param string $owner
	 * @return Brand
	 */
	public function setOwner(string $owner): self
	{
		$this->owner = $owner;
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
	 * @return Brand
	 */
	public function setCode(string $code): self
	{
		$this->code = $code;
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
	 * @return Brand
	 */
	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getApplicationName(): string
	{
		return $this->applicationName;
	}

	/**
	 * @param string $applicationName
	 * @return Brand
	 */
	public function setApplicationName(string $applicationName): self
	{
		$this->applicationName = $applicationName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDisplayUrl(): string
	{
		return $this->displayUrl;
	}

	/**
	 * @param string $displayUrl
	 * @return Brand
	 */
	public function setDisplayUrl(string $displayUrl): self
	{
		$this->displayUrl = $displayUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getWebUrl(): string
	{
		return $this->webUrl;
	}

	/**
	 * @param string $webUrl
	 * @return Brand
	 */
	public function setWebUrl(string $webUrl): self
	{
		$this->webUrl = $webUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOnlineDesignerUrl(): string
	{
		return $this->onlineDesignerUrl;
	}

	/**
	 * @param string $onlineDesignerUrl
	 * @return Brand
	 */
	public function setOnlineDesignerUrl(string $onlineDesignerUrl): self
	{
		$this->onlineDesignerUrl = $onlineDesignerUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOnlineDesignerLogoutUrl(): string
	{
		return $this->onlineDesignerLogoutUrl;
	}

	/**
	 * @param string $onlineDesignerLogoutUrl
	 * @return Brand
	 */
	public function setOnlineDesignerLogoutUrl(string $onlineDesignerLogoutUrl): self
	{
		$this->onlineDesignerLogoutUrl = $onlineDesignerLogoutUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMainWebsiteUrl(): string
	{
		return $this->mainWebsiteUrl;
	}

	/**
	 * @param string $mainWebsiteUrl
	 * @return Brand
	 */
	public function setMainWebsiteUrl(string $mainWebsiteUrl): self
	{
		$this->mainWebsiteUrl = $mainWebsiteUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMacDownloadUrl(): string
	{
		return $this->macDownloadUrl;
	}

	/**
	 * @param string $macDownloadUrl
	 * @return Brand
	 */
	public function setMacDownloadUrl(string $macDownloadUrl): self
	{
		$this->macDownloadUrl = $macDownloadUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getWin32DownloadUrl(): string
	{
		return $this->win32DownloadUrl;
	}

	/**
	 * @param string $win32DownloadUrl
	 * @return Brand
	 */
	public function setWin32DownloadUrl(string $win32DownloadUrl): self
	{
		$this->win32DownloadUrl = $win32DownloadUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSupportTelephoneNumber(): string
	{
		return $this->supportTelephoneNumber;
	}

	/**
	 * @param string $supportTelephoneNumber
	 * @return Brand
	 */
	public function setSupportTelephoneNumber(string $supportTelephoneNumber): self
	{
		$this->supportTelephoneNumber = $supportTelephoneNumber;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSupportEmailAddress(): string
	{
		return $this->supportEmailAddress;
	}

	/**
	 * @param string $supportEmailAddress
	 * @return Brand
	 */
	public function setSupportEmailAddress(string $supportEmailAddress): self
	{
		$this->supportEmailAddress = $supportEmailAddress;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getDefaultCommunicationPreference(): int
	{
		return $this->defaultCommunicationPreference;
	}

	/**
	 * @param int $defaultCommunicationPreference
	 * @return Brand
	 */
	public function setDefaultCommunicationPreference(int $defaultCommunicationPreference): self
	{
		$this->defaultCommunicationPreference = $defaultCommunicationPreference;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isRegisterUsingEmail(): bool
	{
		return $this->registerUsingEmail;
	}

	/**
	 * @param bool $registerUsingEmail
	 * @return Brand
	 */
	public function setRegisterUsingEmail(bool $registerUsingEmail): self
	{
		$this->registerUsingEmail = $registerUsingEmail;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getShareByEmailMethod(): int
	{
		return $this->shareByEmailMethod;
	}

	/**
	 * @param int $shareByEmailMethod
	 * @return Brand
	 */
	public function setShareByEmailMethod(int $shareByEmailMethod): self
	{
		$this->shareByEmailMethod = $shareByEmailMethod;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isOrderFromPreview(): bool
	{
		return $this->orderFromPreview;
	}

	/**
	 * @param bool $orderFromPreview
	 * @return Brand
	 */
	public function setOrderFromPreview(bool $orderFromPreview): self
	{
		$this->orderFromPreview = $orderFromPreview;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isShareHideBranding(): bool
	{
		return $this->shareHideBranding;
	}

	/**
	 * @param bool $shareHideBranding
	 * @return Brand
	 */
	public function setShareHideBranding(bool $shareHideBranding): self
	{
		$this->shareHideBranding = $shareHideBranding;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPreviewDomainUrl(): string
	{
		return $this->previewDomainUrl;
	}

	/**
	 * @param string $previewDomainUrl
	 * @return Brand
	 */
	public function setPreviewDomainUrl(string $previewDomainUrl): self
	{
		$this->previewDomainUrl = $previewDomainUrl;
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
	 * @return Brand
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
	 * @return Brand
	 */
	public function setPaymentMethods(string $paymentMethods): self
	{
		$this->paymentMethods = $paymentMethods;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPaymentIntegration(): string
	{
		return $this->paymentIntegration;
	}

	/**
	 * @param string $paymentIntegration
	 * @return Brand
	 */
	public function setPaymentIntegration(string $paymentIntegration): self
	{
		$this->paymentIntegration = $paymentIntegration;
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
	 * @return Brand
	 */
	public function setAllowGiftCards(bool $allowGiftCards): self
	{
		$this->allowGiftCards = $allowGiftCards;
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
	 * @return Brand
	 */
	public function setAllowVouchers(bool $allowVouchers): self
	{
		$this->allowVouchers = $allowVouchers;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDefaultEmailSettings(): bool
	{
		return $this->useDefaultEmailSettings;
	}

	/**
	 * @param bool $useDefaultEmailSettings
	 * @return Brand
	 */
	public function setUseDefaultEmailSettings(bool $useDefaultEmailSettings): self
	{
		$this->useDefaultEmailSettings = $useDefaultEmailSettings;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpAddress(): string
	{
		return $this->smtpAddress;
	}

	/**
	 * @param string $smtpAddress
	 * @return Brand
	 */
	public function setSmtpAddress(string $smtpAddress): self
	{
		$this->smtpAddress = $smtpAddress;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSmtpPort(): int
	{
		return $this->smtpPort;
	}

	/**
	 * @param int $smtpPort
	 * @return Brand
	 */
	public function setSmtpPort(int $smtpPort): self
	{
		$this->smtpPort = $smtpPort;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSmtpAuth(): bool
	{
		return $this->smtpAuth;
	}

	/**
	 * @param bool $smtpAuth
	 * @return Brand
	 */
	public function setSmtpAuth(bool $smtpAuth): self
	{
		$this->smtpAuth = $smtpAuth;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpAuthUserName(): string
	{
		return $this->smtpAuthUserName;
	}

	/**
	 * @param string $smtpAuthUserName
	 * @return Brand
	 */
	public function setSmtpAuthUserName(string $smtpAuthUserName): self
	{
		$this->smtpAuthUserName = $smtpAuthUserName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpAuthPassword(): string
	{
		return $this->smtpAuthPassword;
	}

	/**
	 * @param string $smtpAuthPassword
	 * @return Brand
	 */
	public function setSmtpAuthPassword(string $smtpAuthPassword): self
	{
		$this->smtpAuthPassword = $smtpAuthPassword;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpType(): string
	{
		return $this->smtpType;
	}

	/**
	 * @param string $smtpType
	 * @return Brand
	 */
	public function setSmtpType(string $smtpType): self
	{
		$this->smtpType = $smtpType;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpSystemFromName(): string
	{
		return $this->smtpSystemFromName;
	}

	/**
	 * @param string $smtpSystemFromName
	 * @return Brand
	 */
	public function setSmtpSystemFromName(string $smtpSystemFromName): self
	{
		$this->smtpSystemFromName = $smtpSystemFromName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpSystemFromAddress(): string
	{
		return $this->smtpSystemFromAddress;
	}

	/**
	 * @param string $smtpSystemFromAddress
	 * @return Brand
	 */
	public function setSmtpSystemFromAddress(string $smtpSystemFromAddress): self
	{
		$this->smtpSystemFromAddress = $smtpSystemFromAddress;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpSystemReplyToName(): string
	{
		return $this->smtpSystemReplyToName;
	}

	/**
	 * @param string $smtpSystemReplyToName
	 * @return Brand
	 */
	public function setSmtpSystemReplyToName(string $smtpSystemReplyToName): self
	{
		$this->smtpSystemReplyToName = $smtpSystemReplyToName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpSystemReplyToAddress(): string
	{
		return $this->smtpSystemReplyToAddress;
	}

	/**
	 * @param string $smtpSystemReplyToAddress
	 * @return Brand
	 */
	public function setSmtpSystemReplyToAddress(string $smtpSystemReplyToAddress): self
	{
		$this->smtpSystemReplyToAddress = $smtpSystemReplyToAddress;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpAdminName(): string
	{
		return $this->smtpAdminName;
	}

	/**
	 * @param string $smtpAdminName
	 * @return Brand
	 */
	public function setSmtpAdminName(string $smtpAdminName): self
	{
		$this->smtpAdminName = $smtpAdminName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpAdminAddress(): string
	{
		return $this->smtpAdminAddress;
	}

	/**
	 * @param string $smtpAdminAddress
	 * @return Brand
	 */
	public function setSmtpAdminAddress(string $smtpAdminAddress): self
	{
		$this->smtpAdminAddress = $smtpAdminAddress;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSmtpAdminActive(): int
	{
		return $this->smtpAdminActive;
	}

	/**
	 * @param int $smtpAdminActive
	 * @return Brand
	 */
	public function setSmtpAdminActive(int $smtpAdminActive): self
	{
		$this->smtpAdminActive = $smtpAdminActive;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpProductionName(): string
	{
		return $this->smtpProductionName;
	}

	/**
	 * @param string $smtpProductionName
	 * @return Brand
	 */
	public function setSmtpProductionName(string $smtpProductionName): self
	{
		$this->smtpProductionName = $smtpProductionName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpProductionAddress(): string
	{
		return $this->smtpProductionAddress;
	}

	/**
	 * @param string $smtpProductionAddress
	 * @return Brand
	 */
	public function setSmtpProductionAddress(string $smtpProductionAddress): self
	{
		$this->smtpProductionAddress = $smtpProductionAddress;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSmtpProductionActive(): int
	{
		return $this->smtpProductionActive;
	}

	/**
	 * @param int $smtpProductionActive
	 * @return Brand
	 */
	public function setSmtpProductionActive(int $smtpProductionActive): self
	{
		$this->smtpProductionActive = $smtpProductionActive;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpOrderConfirmationName(): string
	{
		return $this->smtpOrderConfirmationName;
	}

	/**
	 * @param string $smtpOrderConfirmationName
	 * @return Brand
	 */
	public function setSmtpOrderConfirmationName(string $smtpOrderConfirmationName): self
	{
		$this->smtpOrderConfirmationName = $smtpOrderConfirmationName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpOrderConfirmationAddress(): string
	{
		return $this->smtpOrderConfirmationAddress;
	}

	/**
	 * @param string $smtpOrderConfirmationAddress
	 * @return Brand
	 */
	public function setSmtpOrderConfirmationAddress(string $smtpOrderConfirmationAddress): self
	{
		$this->smtpOrderConfirmationAddress = $smtpOrderConfirmationAddress;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSmtpOrderConfirmationActive(): int
	{
		return $this->smtpOrderConfirmationActive;
	}

	/**
	 * @param int $smtpOrderConfirmationActive
	 * @return Brand
	 */
	public function setSmtpOrderConfirmationActive(int $smtpOrderConfirmationActive): self
	{
		$this->smtpOrderConfirmationActive = $smtpOrderConfirmationActive;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpSaveOrderName(): string
	{
		return $this->smtpSaveOrderName;
	}

	/**
	 * @param string $smtpSaveOrderName
	 * @return Brand
	 */
	public function setSmtpSaveOrderName(string $smtpSaveOrderName): self
	{
		$this->smtpSaveOrderName = $smtpSaveOrderName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpSaveOrderAddress(): string
	{
		return $this->smtpSaveOrderAddress;
	}

	/**
	 * @param string $smtpSaveOrderAddress
	 * @return Brand
	 */
	public function setSmtpSaveOrderAddress(string $smtpSaveOrderAddress): self
	{
		$this->smtpSaveOrderAddress = $smtpSaveOrderAddress;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSmtpSaveOrderActive(): int
	{
		return $this->smtpSaveOrderActive;
	}

	/**
	 * @param int $smtpSaveOrderActive
	 * @return Brand
	 */
	public function setSmtpSaveOrderActive(int $smtpSaveOrderActive): self
	{
		$this->smtpSaveOrderActive = $smtpSaveOrderActive;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpShippingName(): string
	{
		return $this->smtpShippingName;
	}

	/**
	 * @param string $smtpShippingName
	 * @return Brand
	 */
	public function setSmtpShippingName(string $smtpShippingName): self
	{
		$this->smtpShippingName = $smtpShippingName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpShippingAddress(): string
	{
		return $this->smtpShippingAddress;
	}

	/**
	 * @param string $smtpShippingAddress
	 * @return Brand
	 */
	public function setSmtpShippingAddress(string $smtpShippingAddress): self
	{
		$this->smtpShippingAddress = $smtpShippingAddress;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSmtpShippingactive(): int
	{
		return $this->smtpShippingactive;
	}

	/**
	 * @param int $smtpShippingactive
	 * @return Brand
	 */
	public function setSmtpShippingactive(int $smtpShippingactive): self
	{
		$this->smtpShippingactive = $smtpShippingactive;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpNewAccountName(): string
	{
		return $this->smtpNewAccountName;
	}

	/**
	 * @param string $smtpNewAccountName
	 * @return Brand
	 */
	public function setSmtpNewAccountName(string $smtpNewAccountName): self
	{
		$this->smtpNewAccountName = $smtpNewAccountName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpNewAccountAddress(): string
	{
		return $this->smtpNewAccountAddress;
	}

	/**
	 * @param string $smtpNewAccountAddress
	 * @return Brand
	 */
	public function setSmtpNewAccountAddress(string $smtpNewAccountAddress): self
	{
		$this->smtpNewAccountAddress = $smtpNewAccountAddress;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSmtpNewAccountActive(): int
	{
		return $this->smtpNewAccountActive;
	}

	/**
	 * @param int $smtpNewAccountActive
	 * @return Brand
	 */
	public function setSmtpNewAccountActive(int $smtpNewAccountActive): self
	{
		$this->smtpNewAccountActive = $smtpNewAccountActive;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpResetPasswordName(): string
	{
		return $this->smtpResetPasswordName;
	}

	/**
	 * @param string $smtpResetPasswordName
	 * @return Brand
	 */
	public function setSmtpResetPasswordName(string $smtpResetPasswordName): self
	{
		$this->smtpResetPasswordName = $smtpResetPasswordName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpResetPasswordAddress(): string
	{
		return $this->smtpResetPasswordAddress;
	}

	/**
	 * @param string $smtpResetPasswordAddress
	 * @return Brand
	 */
	public function setSmtpResetPasswordAddress(string $smtpResetPasswordAddress): self
	{
		$this->smtpResetPasswordAddress = $smtpResetPasswordAddress;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSmtpResetPasswordActive(): int
	{
		return $this->smtpResetPasswordActive;
	}

	/**
	 * @param int $smtpResetPasswordActive
	 * @return Brand
	 */
	public function setSmtpResetPasswordActive(int $smtpResetPasswordActive): self
	{
		$this->smtpResetPasswordActive = $smtpResetPasswordActive;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpOrderUploadedName(): string
	{
		return $this->smtpOrderUploadedName;
	}

	/**
	 * @param string $smtpOrderUploadedName
	 * @return Brand
	 */
	public function setSmtpOrderUploadedName(string $smtpOrderUploadedName): self
	{
		$this->smtpOrderUploadedName = $smtpOrderUploadedName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSmtpOrderUploadedAddress(): string
	{
		return $this->smtpOrderUploadedAddress;
	}

	/**
	 * @param string $smtpOrderUploadedAddress
	 * @return Brand
	 */
	public function setSmtpOrderUploadedAddress(string $smtpOrderUploadedAddress): self
	{
		$this->smtpOrderUploadedAddress = $smtpOrderUploadedAddress;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSmtpOrderUploadedActive(): int
	{
		return $this->smtpOrderUploadedActive;
	}

	/**
	 * @param int $smtpOrderUploadedActive
	 * @return Brand
	 */
	public function setSmtpOrderUploadedActive(int $smtpOrderUploadedActive): self
	{
		$this->smtpOrderUploadedActive = $smtpOrderUploadedActive;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPreviewLicenseKey(): string
	{
		return $this->previewLicenseKey;
	}

	/**
	 * @param string $previewLicenseKey
	 * @return Brand
	 */
	public function setPreviewLicenseKey(string $previewLicenseKey): self
	{
		$this->previewLicenseKey = $previewLicenseKey;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPreviewExpires(): int
	{
		return $this->previewExpires;
	}

	/**
	 * @param int $previewExpires
	 * @return Brand
	 */
	public function setPreviewExpires(int $previewExpires): self
	{
		$this->previewExpires = $previewExpires;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPreviewExpiresDays(): int
	{
		return $this->previewExpiresDays;
	}

	/**
	 * @param int $previewExpiresDays
	 * @return Brand
	 */
	public function setPreviewExpiresDays(int $previewExpiresDays): self
	{
		$this->previewExpiresDays = $previewExpiresDays;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getOnlineDesignerSigninRegisterPromptDelay(): int
	{
		return $this->onlineDesignerSigninRegisterPromptDelay;
	}

	/**
	 * @param int $onlineDesignerSigninRegisterPromptDelay
	 * @return Brand
	 */
	public function setOnlineDesignerSigninRegisterPromptDelay(int $onlineDesignerSigninRegisterPromptDelay): self
	{
		$this->onlineDesignerSigninRegisterPromptDelay = $onlineDesignerSigninRegisterPromptDelay;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getOnlineDataRetentionPolicy(): int
	{
		return $this->onlineDataRetentionPolicy;
	}

	/**
	 * @param int $onlineDataRetentionPolicy
	 * @return Brand
	 */
	public function setOnlineDataRetentionPolicy(int $onlineDataRetentionPolicy): self
	{
		$this->onlineDataRetentionPolicy = $onlineDataRetentionPolicy;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isOnlineDesignerUseMultilineWorkflow(): bool
	{
		return $this->onlineDesignerUseMultilineWorkflow;
	}

	/**
	 * @param bool $onlineDesignerUseMultilineWorkflow
	 * @return Brand
	 */
	public function setOnlineDesignerUseMultilineWorkflow(bool $onlineDesignerUseMultilineWorkflow): self
	{
		$this->onlineDesignerUseMultilineWorkflow = $onlineDesignerUseMultilineWorkflow;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getGoogleAnalyticsCode(): string
	{
		return $this->googleAnalyticsCode;
	}

	/**
	 * @param string $googleAnalyticsCode
	 * @return Brand
	 */
	public function setGoogleAnalyticsCode(string $googleAnalyticsCode): self
	{
		$this->googleAnalyticsCode = $googleAnalyticsCode;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isGoogleAnalyticsUserIdTracking(): bool
	{
		return $this->googleAnalyticsUserIdTracking;
	}

	/**
	 * @param bool $googleAnalyticsUserIdTracking
	 * @return Brand
	 */
	public function setGoogleAnalyticsUserIdTracking(bool $googleAnalyticsUserIdTracking): self
	{
		$this->googleAnalyticsUserIdTracking = $googleAnalyticsUserIdTracking;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getGoogleTagManagerOnlineCode(): string
	{
		return $this->googleTagManagerOnlineCode;
	}

	/**
	 * @param string $googleTagManagerOnlineCode
	 * @return Brand
	 */
	public function setGoogleTagManagerOnlineCode(string $googleTagManagerOnlineCode): self
	{
		$this->googleTagManagerOnlineCode = $googleTagManagerOnlineCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getGoogleTagManagerCCode(): string
	{
		return $this->googleTagManagerCCode;
	}

	/**
	 * @param string $googleTagManagerCCode
	 * @return Brand
	 */
	public function setGoogleTagManagerCCode(string $googleTagManagerCCode): self
	{
		$this->googleTagManagerCCode = $googleTagManagerCCode;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductCategoryAssetId(): int
	{
		return $this->productCategoryAssetId;
	}

	/**
	 * @param int $productCategoryAssetId
	 * @return Brand
	 */
	public function setProductCategoryAssetId(int $productCategoryAssetId): self
	{
		$this->productCategoryAssetId = $productCategoryAssetId;
		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getProductCategoryAssetVersion(): ?DateTime
	{
		return $this->productCategoryAssetVersion;
	}

	/**
	 * @param DateTime|null $productCategoryAssetVersion
	 * @return Brand
	 */
	public function setProductCategoryAssetVersion(?DateTime $productCategoryAssetVersion): self
	{
		$this->productCategoryAssetVersion = $productCategoryAssetVersion;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCalendarDataAssetId(): int
	{
		return $this->calendarDataAssetId;
	}

	/**
	 * @param int $calendarDataAssetId
	 * @return Brand
	 */
	public function setCalendarDataAssetId(int $calendarDataAssetId): self
	{
		$this->calendarDataAssetId = $calendarDataAssetId;
		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getCalendarDataAssetVersion(): ?DateTime
	{
		return $this->calendarDataAssetVersion;
	}

	/**
	 * @param DateTime|null $calendarDataAssetVersion
	 * @return Brand
	 */
	public function setCalendarDataAssetVersion(?DateTime $calendarDataAssetVersion): self
	{
		$this->calendarDataAssetVersion = $calendarDataAssetVersion;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRedactionMode(): int
	{
		return $this->redactionMode;
	}

	/**
	 * @param int $redactionMode
	 * @return Brand
	 */
	public function setRedactionMode(int $redactionMode): self
	{
		$this->redactionMode = $redactionMode;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAutomaticRedactionEnabled(): bool
	{
		return $this->automaticRedactionEnabled;
	}

	/**
	 * @param bool $automaticRedactionEnabled
	 * @return Brand
	 */
	public function setAutomaticRedactionEnabled(bool $automaticRedactionEnabled): self
	{
		$this->automaticRedactionEnabled = $automaticRedactionEnabled;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getAutomaticRedactionDays(): int
	{
		return $this->automaticRedactionDays;
	}

	/**
	 * @param int $automaticRedactionDays
	 * @return Brand
	 */
	public function setAutomaticRedactionDays(int $automaticRedactionDays): self
	{
		$this->automaticRedactionDays = $automaticRedactionDays;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRedactionNotificationDays(): int
	{
		return $this->redactionNotificationDays;
	}

	/**
	 * @param int $redactionNotificationDays
	 * @return Brand
	 */
	public function setRedactionNotificationDays(int $redactionNotificationDays): self
	{
		$this->redactionNotificationDays = $redactionNotificationDays;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getOrderRedactionDays(): int
	{
		return $this->orderRedactionDays;
	}

	/**
	 * @param int $orderRedactionDays
	 * @return Brand
	 */
	public function setOrderRedactionDays(int $orderRedactionDays): self
	{
		$this->orderRedactionDays = $orderRedactionDays;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getOrderRedactionMode(): int
	{
		return $this->orderRedactionMode;
	}

	/**
	 * @param int $orderRedactionMode
	 * @return Brand
	 */
	public function setOrderRedactionMode(int $orderRedactionMode): self
	{
		$this->orderRedactionMode = $orderRedactionMode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAuCacheVersionMasks(): string
	{
		return $this->auCacheVersionMasks;
	}

	/**
	 * @param string $auCacheVersionMasks
	 * @return Brand
	 */
	public function setAuCacheVersionMasks(string $auCacheVersionMasks): self
	{
		$this->auCacheVersionMasks = $auCacheVersionMasks;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAuCacheVersionBackgrounds(): string
	{
		return $this->auCacheVersionBackgrounds;
	}

	/**
	 * @param string $auCacheVersionBackgrounds
	 * @return Brand
	 */
	public function setAuCacheVersionBackgrounds(string $auCacheVersionBackgrounds): self
	{
		$this->auCacheVersionBackgrounds = $auCacheVersionBackgrounds;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAuCacheVersionScrapbook(): string
	{
		return $this->auCacheVersionScrapbook;
	}

	/**
	 * @param string $auCacheVersionScrapbook
	 * @return Brand
	 */
	public function setAuCacheVersionScrapbook(string $auCacheVersionScrapbook): self
	{
		$this->auCacheVersionScrapbook = $auCacheVersionScrapbook;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAuCacheVersionFrames(): string
	{
		return $this->auCacheVersionFrames;
	}

	/**
	 * @param string $auCacheVersionFrames
	 * @return Brand
	 */
	public function setAuCacheVersionFrames(string $auCacheVersionFrames): self
	{
		$this->auCacheVersionFrames = $auCacheVersionFrames;
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
	 * @return Brand
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
	 * @return Brand
	 */
	public function setImageScalingBeforeEnabled(bool $imageScalingBeforeEnabled): self
	{
		$this->imageScalingBeforeEnabled = $imageScalingBeforeEnabled;
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
	 * @return Brand
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
	 * @return Brand
	 */
	public function setImageScalingAfterEnabled(bool $imageScalingAfterEnabled): self
	{
		$this->imageScalingAfterEnabled = $imageScalingAfterEnabled;
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
	 * @return Brand
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
	 * @return Brand
	 */
	public function setShowShuffleLayoutOptions(bool $showShuffleLayoutOptions): self
	{
		$this->showShuffleLayoutOptions = $showShuffleLayoutOptions;
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
	 * @return Brand
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
	 * @return Brand
	 */
	public function setEnableSwitchingEditor(bool $enableSwitchingEditor): self
	{
		$this->enableSwitchingEditor = $enableSwitchingEditor;
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
	 * @return Brand
	 */
	public function setOnlineDesignerLogoLinkUrl(string $onlineDesignerLogoLinkUrl): self
	{
		$this->onlineDesignerLogoLinkUrl = $onlineDesignerLogoLinkUrl;
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
	 * @return Brand
	 */
	public function setOnlineDesignerLogoLinkTooltip(string $onlineDesignerLogoLinkTooltip): self
	{
		$this->onlineDesignerLogoLinkTooltip = $onlineDesignerLogoLinkTooltip;
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
	 * @return Brand
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
	 * @return Brand
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
	 * @return Brand
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
	 * @return Brand
	 */
	public function setSmartGuidesPageGuideColour(string $smartGuidesPageGuideColour): self
	{
		$this->smartGuidesPageGuideColour = $smartGuidesPageGuideColour;
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
	 * @return Brand
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
	 * @return Brand
	 */
	public function setAllowUsersToTogglePerfectlyClear(bool $allowUsersToTogglePerfectlyClear): self
	{
		$this->allowUsersToTogglePerfectlyClear = $allowUsersToTogglePerfectlyClear;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOnlineDesignerCDNUrl(): string
	{
		return $this->onlineDesignerCDNUrl;
	}

	/**
	 * @param string $onlineDesignerCDNUrl
	 * @return Brand
	 */
	public function setOnlineDesignerCDNUrl(string $onlineDesignerCDNUrl): self
	{
		$this->onlineDesignerCDNUrl = $onlineDesignerCDNUrl;
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
	 * @return Brand
	 */
	public function setInsertDeleteButtonsVisibility(bool $insertDeleteButtonsVisibility): self
	{
		$this->insertDeleteButtonsVisibility = $insertDeleteButtonsVisibility;
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
	 * @return Brand
	 */
	public function setTotalPagesDropDownMode(int $totalPagesDropDownMode): self
	{
		$this->totalPagesDropDownMode = $totalPagesDropDownMode;
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
	 * @return Brand
	 */

	public function setAveragePicturesPerPage(int $averagePicturesPerPage): self
	{
		$this->averagePicturesPerPage = $averagePicturesPerPage;
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
	 * @return Brand
	 */
	public function setComponentUpsellSettings(int $componentUpsellSettings): self
	{
		$this->componentUpsellSettings = $componentUpsellSettings;
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
	 * @return Brand
	 */
	public function setActive(bool $active): self
	{
		$this->active = $active;
		return $this;
	}

    /**
     * @return string
     */
    public function getOAuthProvider(): string
    {
        return $this->oAuthProvider;
    }

    /**
     * @param string $oAuthProvider
     * @return Brand
     */
    public function setOAuthProvider(string $oAuthProvider): self
    {
        $this->oAuthProvider = $oAuthProvider;
        return $this;
    }

    /**
     * @return string
     */
    public function getOAuthToken(): string
    {
        return $this->oAuthToken;
    }

    /**
     * @param string $oAuthToken
     * @return Brand
     */
    public function setOAuthToken(string $oAuthToken): self
    {
        $this->oAuthToken = $oAuthToken;
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
     * @return Brand
     */
    public function setUseDefaultAccountPagesUrl(bool $useDefaultAccountPagesUrl): self
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
     * @return Brand
     */
    public function setAccountPagesUrl(string $accountPagesUrl): self
    {
        $this->accountPagesUrl = $accountPagesUrl;
        return $this;
    }

    public function getOnlineUiUrl(): string
    {
        return $this->onlineUiUrl;
    }

    public function setOnlineUiUrl(string $onlineUiUrl): self
    {
        $this->onlineUiUrl = $onlineUiUrl;
        return $this;
    }

    public function getOnlineApiUrl(): string
    {
        return $this->onlineApiUrl;
    }

    public function setOnlineApiUrl(string $onlineApiUrl): self
    {
        $this->onlineApiUrl = $onlineApiUrl;
        return $this;
    }

    public function getOnlineAppKeyEntropyValue(): string
    {
        return $this->onlineAppKeyEntropyValue;
    }

    public function setOnlineAppKeyEntropyValue(string $onlineAppKeyEntropyValue): self
    {
        $this->onlineAppKeyEntropyValue = $onlineAppKeyEntropyValue;
        return $this;
    }

    public function getOnlineAboutUrl(): string
    {
        return $this->onlineAboutUrl;
    }

    public function setOnlineAboutUrl(string $onlineAboutUrl): self
    {
        $this->onlineAboutUrl = $onlineAboutUrl;
        return $this;
    }

    public function getOnlineHelpUrl(): string
    {
        return $this->onlineHelpUrl;
    }

    public function setOnlineHelpUrl(string $onlineHelpUrl): self
    {
        $this->onlineHelpUrl = $onlineHelpUrl;
        return $this;
    }

    public function getOnlineTermsAndConditionsUrl(): string
    {
        return $this->onlineTermsAndConditionsUrl;
    }

    public function setOnlineTermsAndConditionsUrl(string $onlineTermsAndConditionsUrl): self
    {
        $this->onlineTermsAndConditionsUrl = $onlineTermsAndConditionsUrl;
        return $this;
    }

    public function getTheme(): int
    {
        return $this->theme;
    }

    public function setTheme(int $theme): self
    {
        $this->theme = $theme;
        return $this;
    }
}
