<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\ProductCollectionLinkRepository;
use Taopix\ControlCentre\Traits\Entity\ToArrayTrait;

#[ORM\Table(name: "productcollectionlink", schema: "controlcentre"), ORM\Index(columns: ["productcode"], name: "productcode")]
#[ORM\Index(columns: ["collectioncode"], name: "collectioncode"), ORM\Entity(repositoryClass: ProductCollectionLinkRepository::class)]
class ProductCollectionLink
{
	use ToArrayTrait;

	#[ORM\Id, ORM\GeneratedValue, ORM\Column(name: "id", nullable: false)]
    private ?int $id = null;

	#[ORM\Column(name: "datecreated", nullable: false)]
    private ?DateTime $dateCreated = null;

	#[ORM\Column(name: "publishversion", type: Types::SMALLINT, nullable: false)]
	private int $publishVersion = 0;

	#[ORM\Column(name: "companycode", length: 50, nullable: false)]
	private string $companyCode = '';

	#[ORM\Column(name: "collectioncode", length: 50, nullable: false)]
	private string $collectionCode = '';

	#[ORM\Column(name: "collectionname", length: 2048, nullable: false)]
	private string $collectionName = '';

	#[ORM\Column(name: "collectiondescription", type: Types::TEXT, length: AbstractMySQLPlatform::LENGTH_LIMIT_MEDIUMTEXT, nullable: false)]
	private string $collectionDescription = '';

	#[ORM\Column(name: "collectionmoreinformationurl", length: 1024, nullable: false)]
	private string $collectionMoreInformationUrl = '';

	#[ORM\Column(name: "collectionthumbnailresourceref", length: 50, nullable: false)]
	private string $collectionThumbnailResourceRef = '';

	#[ORM\Column(name: "collectionthumbnailresourcedatauid", length: 50, nullable: false)]
	private string $collectionThumbnailResourceDataUid = '';

	#[ORM\Column(name: "collectionpreviewresourceref", length: 50, nullable: false)]
	private string $collectionPreviewResourceRef = '';

	#[ORM\Column(name: "collectionpreviewresourcedatauid", length: 50, nullable: false)]
	private string $collectionPreviewResourceDataUid = '';

	#[ORM\Column(name: "collectiontype", type: Types::SMALLINT, nullable: false)]
	private int $collectionType = 0;

	#[ORM\Column(name: "collectionsortlevel", length: 50, nullable: false)]
	private string $collectionSortLevel = '';

	#[ORM\Column(name: "collectiontextengineversion", type: Types::SMALLINT, nullable: false)]
	private int $collectionTextEngineVersion = 0;

	#[ORM\Column(name: "productcode", length: 50, nullable: false)]
	private string $productCode = '';

	#[ORM\Column(name: "productname", length: 2048, nullable: false)]
	private string $productName = '';

	#[ORM\Column(name: "productdescription", type: Types::TEXT, length: AbstractMySQLPlatform::LENGTH_LIMIT_MEDIUMTEXT, nullable: false)]
	private string $productDescription = '';

	#[ORM\Column(name: "productmoreinformationurl", length: 1024, nullable: false)]
	private string $productMoreInformationUrl = '';

	#[ORM\Column(name: "productthumbnailresourceref", length: 50, nullable: false)]
	private string $productThumbnailResourceRef = '';

	#[ORM\Column(name: "productthumbnailresourcedatauid", length: 50, nullable: false)]
	private string $productThumbnailResourceDataUid = '';

	#[ORM\Column(name: "productpreviewresourceref", length: 50, nullable: false)]
	private string $productPreviewResourceRef = '';

	#[ORM\Column(name: "productpreviewresourcedatauid", length: 50, nullable: false)]
	private string $productPreviewResourceDataUid = '';

	#[ORM\Column(name: "producthasdimensions", nullable: false)]
	private bool $productHasDimensions = false;

	#[ORM\Column(name: "productminpagecount", nullable: false)]
	private int $productMinPageCount = 0;

	#[ORM\Column(name: "productmaxpagecount", nullable: false)]
	private int $productMaxPageCount = 0;

	#[ORM\Column(name: "productdefaultpagecount", nullable: false)]
	private int $productDefaultPageCount = 0;

	#[ORM\Column(name: "productpageinsertcount", nullable: false)]
	private int $productPageInsertCount = 0;

	#[ORM\Column(name: "productpagepaperwidth", length: 25, nullable: false)]
	private string $productPagePaperWidth = '0';

	#[ORM\Column(name: "productpagepaperheight", length: 25, nullable: false)]
	private string $productPagePaperHeight = '0';

	#[ORM\Column(name: "productpagebleed", length: 25, nullable: false)]
	private string $productPageBleed = '0';

	#[ORM\Column(name: "productpageisspreads", nullable: false)]
	private bool $productPageIsSpreads = false;

	#[ORM\Column(name: "productpageinsidebleed", nullable: false)]
	private bool $productPageInsideBleed = false;

	#[ORM\Column(name: "productpagesafemargin", length: 25, nullable: false)]
	private string $productPageSafeMargin = '0';

	#[ORM\Column(name: "productpagewidth", length: 25, nullable: false)]
	private string $productPageWidth = '0';

	#[ORM\Column(name: "productpageheight", length: 25, nullable: false)]
	private string $productPageHeight = '0';

	#[ORM\Column(name: "productpagefirstpage", nullable: false)]
	private int $productPageFirstPage = 0;

	#[ORM\Column(name: "productcover1active", nullable: false)]
	private bool $productCover1Active = false;

	#[ORM\Column(name: "productcover1type", nullable: false)]
	private int $productCover1Type = 0;

	#[ORM\Column(name: "productcover1paperwidth", length: 25, nullable: false)]
	private string $productCover1PaperWidth = '0';

	#[ORM\Column(name: "productcover1paperheight", length: 25, nullable: false)]
	private string $productCover1PaperHeight = '0';

	#[ORM\Column(name: "productcover1bleed", length: 25, nullable: false)]
	private string $productCover1Bleed = '0';

	#[ORM\Column(name: "productcover1safemargin", length: 25, nullable: false)]
	private string $productCover1SafeMargin = '0';

	#[ORM\Column(name: "productcover1backflap", length: 25, nullable: false)]
	private string $productCover1BackFlap = '0';

	#[ORM\Column(name: "productcover1frontflap", length: 25, nullable: false)]
	private string $productCover1FrontFlap = '0';

	#[ORM\Column(name: "productcover1wraparound", length: 25, nullable: false)]
	private string $productCover1WrapAround = '0';

	#[ORM\Column(name: "productcover1spine", length: 25, nullable: false)]
	private string $productCover1Spine = '0';

	#[ORM\Column(name: "productcover1flexiblespine", nullable: false)]
	private bool $productCover1FlexibleSpine = false;

	#[ORM\Column(name: "productcover1width", length: 25, nullable: false)]
	private string $productCover1Width = '0';

	#[ORM\Column(name: "productcover1height", length: 25, nullable: false)]
	private string $productCover1Height = '0';

	#[ORM\Column(name: "productcover1flexiblespinedata", length: 4096, nullable: false)]
	private string $productCover1FlexibleSpineData = '';

	#[ORM\Column(name: "productcover2active", length: 25, nullable: false)]
	private bool $productCover2Active = false;

	#[ORM\Column(name: "productcover2paperwidth", length: 25, nullable: false)]
	private string $productCover2PaperWidth = '0';

	#[ORM\Column(name: "productcover2paperheight", length: 25, nullable: false)]
	private string $productCover2PaperHeight = '0';

	#[ORM\Column(name: "productcover2bleed", length: 25, nullable: false)]
	private string $productCover2Bleed = '0';

	#[ORM\Column(name: "productcover2safemargin", length: 25, nullable: false)]
	private string $productCover2SafeMargin = '0';

	#[ORM\Column(name: "productcover2width", length: 25, nullable: false)]
	private string $productCover2Width = '0';

	#[ORM\Column(name: "productcover2height", length: 25, nullable: false)]
	private string $productCover2Height = '0';

	#[ORM\Column(name: "productselectormodedesktop", type: Types::SMALLINT, nullable: false)]
	private int $productSelectorModeDesktop = 0;

	#[ORM\Column(name: "productwizardmodeonline", type: Types::SMALLINT, nullable: false)]
	private int $productWizardModeOnline = 0;

	#[ORM\Column(name: "productaimodedesktop", type: Types::SMALLINT, nullable: false)]
	private int $productAiModeDesktop = 0;

	#[ORM\Column(name: "productaimodeonline", type: Types::SMALLINT, nullable: false)]
	private int $productAiModeOnline = 0;

	#[ORM\Column(name: "productcalendarlocale", length: 25, nullable: false)]
	private string $productCalendarLocale = '';

	#[ORM\Column(name: "productcalendarlocalecanchange", nullable: false)]
	private bool $productCalendarLocaleCanChange = false;

	#[ORM\Column(name: "productcalendarstartday", type: Types::SMALLINT, nullable: false)]
	private int $productCalendarStartDay = 0;

	#[ORM\Column(name: "productcalendarstartdaycanchange", nullable: false)]
	private bool $productCalendarStartDayCanChange = false;

	#[ORM\Column(name: "productcalendarstartmonth", type: Types::SMALLINT, nullable: false)]
	private int $productCalendarStartMonth = 0;

	#[ORM\Column(name: "productcalendarstartmonthcanchange", nullable: false)]
	private bool $productCalendarStartMonthCanChange = false;

	#[ORM\Column(name: "productcalendarstartyear", nullable: false)]
	private int $productCalendarStartYear = 0;

	#[ORM\Column(name: "productcalendarstartyearcanchange", nullable: false)]
	private bool $productCalendarStartYearCanChange = false;

	#[ORM\Column(name: "productsortorder", nullable: false)]
	private int $productSortOrder = 0;

	#[ORM\Column(name: "producttarget", type: Types::SMALLINT, nullable: false)]
	private int $productTarget = 0;

	#[ORM\Column(name: "availabledesktop", nullable: false)]
	private bool $availableDesktop = false;

	#[ORM\Column(name: "hasbeenavailabledesktop", nullable: false)]
	private bool $hasBeenAvailableDesktop = false;

	#[ORM\Column(name: "availableonline", nullable: false)]
	private bool $availableOnline = false;

    #[ORM\Column(name: 'productconfigurationflags')]
    private int $productConfigurationFlags = 0;

    #[ORm\Column(name: 'productpagecontentassignmode')]
    private int $productPageContentAssignMode = 0;

    #[ORM\Column(name: 'collectionsummary', length: 4096)]
    private string $collectionSummary = '';

    #[ORM\Column(name: 'productOrientation')]
    private int $productOrientation = 0;

    #[ORM\Column(name: 'productSizeCode', length: 25)]
    private string $productSizeCode = '';

    #[ORM\Column(name: 'productsizename', type: Types::TEXT, length: AbstractMySQLPlatform::LENGTH_LIMIT_MEDIUMTEXT - 1)]
    private string $productSizeName = '';

    #[ORM\Column(name: 'productsizearea')]
    private int $productSizeArea = 0;

    #[ORM\Column(name: 'collectionthumbnailresourcedevicepixelratio')]
    private int $collectionThumbnailResourceDevicePixelRatio = 1;

    #[ORM\Column(name: 'collectionpreviewresourcedevicepixelratio')]
    private int $collectionPreviewResourceDevicePixelRatio = 1;

    #[ORM\Column(name: 'productthumbnailresourcedevicepixelratio')]
    private int $productThumbnailResourceDevicePixelRatio = 1;

    #[ORM\Column(name: 'productpreviewresourcedevicepixelratio')]
    private int $productPreviewResourceDevicePixelRatio = 1;

	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return ProductCollectionLink
	 */
	public function setId(int $id): ProductCollectionLink
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
	 * @return ProductCollectionLink
	 */
	public function setDateCreated(DateTime $dateCreated): ProductCollectionLink
	{
		$this->dateCreated = $dateCreated;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPublishVersion(): int
	{
		return $this->publishVersion;
	}

	/**
	 * @param int $publishVersion
	 * @return ProductCollectionLink
	 */
	public function setPublishVersion(int $publishVersion): ProductCollectionLink
	{
		$this->publishVersion = $publishVersion;
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
	 * @return ProductCollectionLink
	 */
	public function setCompanyCode(string $companyCode): ProductCollectionLink
	{
		$this->companyCode = $companyCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCollectionCode(): string
	{
		return $this->collectionCode;
	}

	/**
	 * @param string $collectionCode
	 * @return ProductCollectionLink
	 */
	public function setCollectionCode(string $collectionCode): ProductCollectionLink
	{
		$this->collectionCode = $collectionCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCollectionName(): string
	{
		return $this->collectionName;
	}

	/**
	 * @param string $collectionName
	 * @return ProductCollectionLink
	 */
	public function setCollectionName(string $collectionName): ProductCollectionLink
	{
		$this->collectionName = $collectionName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCollectionDescription(): string
	{
		return $this->collectionDescription;
	}

	/**
	 * @param string $collectionDescription
	 * @return ProductCollectionLink
	 */
	public function setCollectionDescription(string $collectionDescription): ProductCollectionLink
	{
		$this->collectionDescription = $collectionDescription;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCollectionMoreInformationUrl(): string
	{
		return $this->collectionMoreInformationUrl;
	}

	/**
	 * @param string $collectionMoreInformationUrl
	 * @return ProductCollectionLink
	 */
	public function setCollectionMoreInformationUrl(string $collectionMoreInformationUrl): ProductCollectionLink
	{
		$this->collectionMoreInformationUrl = $collectionMoreInformationUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCollectionThumbnailResourceRef(): string
	{
		return $this->collectionThumbnailResourceRef;
	}

	/**
	 * @param string $collectionThumbnailResourceRef
	 * @return ProductCollectionLink
	 */
	public function setCollectionThumbnailResourceRef(string $collectionThumbnailResourceRef): ProductCollectionLink
	{
		$this->collectionThumbnailResourceRef = $collectionThumbnailResourceRef;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCollectionThumbnailResourceDataUid(): string
	{
		return $this->collectionThumbnailResourceDataUid;
	}

	/**
	 * @param string $collectionThumbnailResourceDataUid
	 * @return ProductCollectionLink
	 */
	public function setCollectionThumbnailResourceDataUid(string $collectionThumbnailResourceDataUid): ProductCollectionLink
	{
		$this->collectionThumbnailResourceDataUid = $collectionThumbnailResourceDataUid;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCollectionPreviewResourceRef(): string
	{
		return $this->collectionPreviewResourceRef;
	}

	/**
	 * @param string $collectionPreviewResourceRef
	 * @return ProductCollectionLink
	 */
	public function setCollectionPreviewResourceRef(string $collectionPreviewResourceRef): ProductCollectionLink
	{
		$this->collectionPreviewResourceRef = $collectionPreviewResourceRef;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCollectionPreviewResourceDataUid(): string
	{
		return $this->collectionPreviewResourceDataUid;
	}

	/**
	 * @param string $collectionPreviewResourceDataUid
	 * @return ProductCollectionLink
	 */
	public function setCollectionPreviewResourceDataUid(string $collectionPreviewResourceDataUid): ProductCollectionLink
	{
		$this->collectionPreviewResourceDataUid = $collectionPreviewResourceDataUid;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCollectionType(): int
	{
		return $this->collectionType;
	}

	/**
	 * @param int $collectionType
	 * @return ProductCollectionLink
	 */
	public function setCollectionType(int $collectionType): ProductCollectionLink
	{
		$this->collectionType = $collectionType;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCollectionSortLevel(): string
	{
		return $this->collectionSortLevel;
	}

	/**
	 * @param string $collectionSortLevel
	 * @return ProductCollectionLink
	 */
	public function setCollectionSortLevel(string $collectionSortLevel): ProductCollectionLink
	{
		$this->collectionSortLevel = $collectionSortLevel;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCollectionTextEngineVersion(): int
	{
		return $this->collectionTextEngineVersion;
	}

	/**
	 * @param int $collectionTextEngineVersion
	 * @return ProductCollectionLink
	 */
	public function setCollectionTextEngineVersion(int $collectionTextEngineVersion): ProductCollectionLink
	{
		$this->collectionTextEngineVersion = $collectionTextEngineVersion;
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
	 * @return ProductCollectionLink
	 */
	public function setProductCode(string $productCode): ProductCollectionLink
	{
		$this->productCode = $productCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductName(): string
	{
		return $this->productName;
	}

	/**
	 * @param string $productName
	 * @return ProductCollectionLink
	 */
	public function setProductName(string $productName): ProductCollectionLink
	{
		$this->productName = $productName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductDescription(): string
	{
		return $this->productDescription;
	}

	/**
	 * @param string $productDescription
	 * @return ProductCollectionLink
	 */
	public function setProductDescription(string $productDescription): ProductCollectionLink
	{
		$this->productDescription = $productDescription;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductMoreInformationUrl(): string
	{
		return $this->productMoreInformationUrl;
	}

	/**
	 * @param string $productMoreInformationUrl
	 * @return ProductCollectionLink
	 */
	public function setProductMoreInformationUrl(string $productMoreInformationUrl): ProductCollectionLink
	{
		$this->productMoreInformationUrl = $productMoreInformationUrl;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductThumbnailResourceRef(): string
	{
		return $this->productThumbnailResourceRef;
	}

	/**
	 * @param string $productThumbnailResourceRef
	 * @return ProductCollectionLink
	 */
	public function setProductThumbnailResourceRef(string $productThumbnailResourceRef): ProductCollectionLink
	{
		$this->productThumbnailResourceRef = $productThumbnailResourceRef;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductThumbnailResourceUid(): string
	{
		return $this->productThumbnailResourceUid;
	}

	/**
	 * @param string $productThumbnailResourceUid
	 * @return ProductCollectionLink
	 */
	public function setProductThumbnailResourceUid(string $productThumbnailResourceUid): ProductCollectionLink
	{
		$this->productThumbnailResourceUid = $productThumbnailResourceUid;
		return $this;
	}


	// Added this get/set as appears productThumbnailResourceUid was changed to productThumbnailResourceDataUid after creation
	/**
	 * @return string
	 */
	public function getProductThumbnailResourceDataUid(): string
	{
		return $this->productThumbnailResourceDataUid;
	}

	/**
	 * @param string $productThumbnailResourceDataUid
	 * @return ProductCollectionLink
	 */
	public function setProductThumbnailResourceDataUid(string $productThumbnailResourceDataUid): ProductCollectionLink
	{
		$this->productThumbnailResourceDataUid = $productThumbnailResourceDataUid;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductPreviewResourceRef(): string
	{
		return $this->productPreviewResourceRef;
	}

	/**
	 * @param string $productPreviewResourceRef
	 * @return ProductCollectionLink
	 */
	public function setProductPreviewResourceRef(string $productPreviewResourceRef): ProductCollectionLink
	{
		$this->productPreviewResourceRef = $productPreviewResourceRef;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductPreviewResourceDataUid(): string
	{
		return $this->productPreviewResourceDataUid;
	}

	/**
	 * @param string $productPreviewResourceDataUid
	 * @return ProductCollectionLink
	 */
	public function setProductPreviewResourceDataUid(string $productPreviewResourceDataUid): ProductCollectionLink
	{
		$this->productPreviewResourceDataUid = $productPreviewResourceDataUid;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isProductHasDimensions(): bool
	{
		return $this->productHasDimensions;
	}

	/**
	 * @param bool $productHasDimensions
	 * @return ProductCollectionLink
	 */
	public function setProductHasDimensions(bool $productHasDimensions): ProductCollectionLink
	{
		$this->productHasDimensions = $productHasDimensions;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductMinPageCount(): int
	{
		return $this->productMinPageCount;
	}

	/**
	 * @param int $productMinPageCount
	 * @return ProductCollectionLink
	 */
	public function setProductMinPageCount(int $productMinPageCount): ProductCollectionLink
	{
		$this->productMinPageCount = $productMinPageCount;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductMaxPageCount(): int
	{
		return $this->productMaxPageCount;
	}

	/**
	 * @param int $productMaxPageCount
	 * @return ProductCollectionLink
	 */
	public function setProductMaxPageCount(int $productMaxPageCount): ProductCollectionLink
	{
		$this->productMaxPageCount = $productMaxPageCount;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductDefaultPageCount(): int
	{
		return $this->productDefaultPageCount;
	}

	/**
	 * @param int $productDefaultPageCount
	 * @return ProductCollectionLink
	 */
	public function setProductDefaultPageCount(int $productDefaultPageCount): ProductCollectionLink
	{
		$this->productDefaultPageCount = $productDefaultPageCount;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductPageInsertCount(): int
	{
		return $this->productPageInsertCount;
	}

	/**
	 * @param int $productPageInsertCount
	 * @return ProductCollectionLink
	 */
	public function setProductPageInsertCount(int $productPageInsertCount): ProductCollectionLink
	{
		$this->productPageInsertCount = $productPageInsertCount;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductPagePaperWidth(): string
	{
		return $this->productPagePaperWidth;
	}

	/**
	 * @param string $productPagePaperWidth
	 * @return ProductCollectionLink
	 */
	public function setProductPagePaperWidth(string $productPagePaperWidth): ProductCollectionLink
	{
		$this->productPagePaperWidth = $productPagePaperWidth;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductPagePaperHeight(): string
	{
		return $this->productPagePaperHeight;
	}

	/**
	 * @param string $productPagePaperHeight
	 * @return ProductCollectionLink
	 */
	public function setProductPagePaperHeight(string $productPagePaperHeight): ProductCollectionLink
	{
		$this->productPagePaperHeight = $productPagePaperHeight;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductPageBleed(): string
	{
		return $this->productPageBleed;
	}

	/**
	 * @param string $productPageBleed
	 * @return ProductCollectionLink
	 */
	public function setProductPageBleed(string $productPageBleed): ProductCollectionLink
	{
		$this->productPageBleed = $productPageBleed;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isProductPageIsSpreads(): bool
	{
		return $this->productPageIsSpreads;
	}

	/**
	 * @param bool $productPageIsSpreads
	 * @return ProductCollectionLink
	 */
	public function setProductPageIsSpreads(bool $productPageIsSpreads): ProductCollectionLink
	{
		$this->productPageIsSpreads = $productPageIsSpreads;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isProductPageInsideBleed(): bool
	{
		return $this->productPageInsideBleed;
	}

	/**
	 * @param bool $productPageInsideBleed
	 * @return ProductCollectionLink
	 */
	public function setProductPageInsideBleed(bool $productPageInsideBleed): ProductCollectionLink
	{
		$this->productPageInsideBleed = $productPageInsideBleed;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductPageSafeMargin(): string
	{
		return $this->productPageSafeMargin;
	}

	/**
	 * @param string $productPageSafeMargin
	 * @return ProductCollectionLink
	 */
	public function setProductPageSafeMargin(string $productPageSafeMargin): ProductCollectionLink
	{
		$this->productPageSafeMargin = $productPageSafeMargin;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductPageWidth(): string
	{
		return $this->productPageWidth;
	}

	/**
	 * @param string $productPageWidth
	 * @return ProductCollectionLink
	 */
	public function setProductPageWidth(string $productPageWidth): ProductCollectionLink
	{
		$this->productPageWidth = $productPageWidth;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductPageHeight(): string
	{
		return $this->productPageHeight;
	}

	/**
	 * @param string $productPageHeight
	 * @return ProductCollectionLink
	 */
	public function setProductPageHeight(string $productPageHeight): ProductCollectionLink
	{
		$this->productPageHeight = $productPageHeight;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductPageFirstPage(): int
	{
		return $this->productPageFirstPage;
	}

	/**
	 * @param int $productPageFirstPage
	 * @return ProductCollectionLink
	 */
	public function setProductPageFirstPage(int $productPageFirstPage): ProductCollectionLink
	{
		$this->productPageFirstPage = $productPageFirstPage;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isProductCover1Active(): bool
	{
		return $this->productCover1Active;
	}

	/**
	 * @param bool $productCover1Active
	 * @return ProductCollectionLink
	 */
	public function setProductCover1Active(bool $productCover1Active): ProductCollectionLink
	{
		$this->productCover1Active = $productCover1Active;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductCover1Type(): int
	{
		return $this->productCover1Type;
	}

	/**
	 * @param int $productCover1Type
	 * @return ProductCollectionLink
	 */
	public function setProductCover1Type(int $productCover1Type): ProductCollectionLink
	{
		$this->productCover1Type = $productCover1Type;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover1PaperWidth(): string
	{
		return $this->productCover1PaperWidth;
	}

	/**
	 * @param string $productCover1PaperWidth
	 * @return ProductCollectionLink
	 */
	public function setProductCover1PaperWidth(string $productCover1PaperWidth): ProductCollectionLink
	{
		$this->productCover1PaperWidth = $productCover1PaperWidth;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover1PaperHeight(): string
	{
		return $this->productCover1PaperHeight;
	}

	/**
	 * @param string $productCover1PaperHeight
	 * @return ProductCollectionLink
	 */
	public function setProductCover1PaperHeight(string $productCover1PaperHeight): ProductCollectionLink
	{
		$this->productCover1PaperHeight = $productCover1PaperHeight;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover1Bleed(): string
	{
		return $this->productCover1Bleed;
	}

	/**
	 * @param string $productCover1Bleed
	 * @return ProductCollectionLink
	 */
	public function setProductCover1Bleed(string $productCover1Bleed): ProductCollectionLink
	{
		$this->productCover1Bleed = $productCover1Bleed;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover1SafeMargin(): string
	{
		return $this->productCover1SafeMargin;
	}

	/**
	 * @param string $productCover1SafeMargin
	 * @return ProductCollectionLink
	 */
	public function setProductCover1SafeMargin(string $productCover1SafeMargin): ProductCollectionLink
	{
		$this->productCover1SafeMargin = $productCover1SafeMargin;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover1BackFlap(): string
	{
		return $this->productCover1BackFlap;
	}

	/**
	 * @param string $productCover1BackFlap
	 * @return ProductCollectionLink
	 */
	public function setProductCover1BackFlap(string $productCover1BackFlap): ProductCollectionLink
	{
		$this->productCover1BackFlap = $productCover1BackFlap;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover1FrontFlap(): string
	{
		return $this->productCover1FrontFlap;
	}

	/**
	 * @param string $productCover1FrontFlap
	 * @return ProductCollectionLink
	 */
	public function setProductCover1FrontFlap(string $productCover1FrontFlap): ProductCollectionLink
	{
		$this->productCover1FrontFlap = $productCover1FrontFlap;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover1WrapAround(): string
	{
		return $this->productCover1WrapAround;
	}

	/**
	 * @param string $productCover1WrapAround
	 * @return ProductCollectionLink
	 */
	public function setProductCover1WrapAround(string $productCover1WrapAround): ProductCollectionLink
	{
		$this->productCover1WrapAround = $productCover1WrapAround;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover1Spine(): string
	{
		return $this->productCover1Spine;
	}

	/**
	 * @param string $productCover1Spine
	 * @return ProductCollectionLink
	 */
	public function setProductCover1Spine(string $productCover1Spine): ProductCollectionLink
	{
		$this->productCover1Spine = $productCover1Spine;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isProductCover1FlexibleSpine(): bool
	{
		return $this->productCover1FlexibleSpine;
	}

	/**
	 * @param bool $productCover1FlexibleSpine
	 * @return ProductCollectionLink
	 */
	public function setProductCover1FlexibleSpine(bool $productCover1FlexibleSpine): ProductCollectionLink
	{
		$this->productCover1FlexibleSpine = $productCover1FlexibleSpine;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover1Width(): string
	{
		return $this->productCover1Width;
	}

	/**
	 * @param string $productCover1Width
	 * @return ProductCollectionLink
	 */
	public function setProductCover1Width(string $productCover1Width): ProductCollectionLink
	{
		$this->productCover1Width = $productCover1Width;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover1Height(): string
	{
		return $this->productCover1Height;
	}

	/**
	 * @param string $productCover1Height
	 * @return ProductCollectionLink
	 */
	public function setProductCover1Height(string $productCover1Height): ProductCollectionLink
	{
		$this->productCover1Height = $productCover1Height;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover1FlexibleSpineData(): string
	{
		return $this->productCover1FlexibleSpineData;
	}

	/**
	 * @param string $productCover1FlexibleSpineData
	 * @return ProductCollectionLink
	 */
	public function setProductCover1FlexibleSpineData(string $productCover1FlexibleSpineData): ProductCollectionLink
	{
		$this->productCover1FlexibleSpineData = $productCover1FlexibleSpineData;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isProductCover2Active(): bool
	{
		return $this->productCover2Active;
	}

	/**
	 * @param bool $productCover2Active
	 * @return ProductCollectionLink
	 */
	public function setProductCover2Active(bool $productCover2Active): ProductCollectionLink
	{
		$this->productCover2Active = $productCover2Active;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover2PaperWidth(): string
	{
		return $this->productCover2PaperWidth;
	}

	/**
	 * @param string $productCover2PaperWidth
	 * @return ProductCollectionLink
	 */
	public function setProductCover2PaperWidth(string $productCover2PaperWidth): ProductCollectionLink
	{
		$this->productCover2PaperWidth = $productCover2PaperWidth;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover2PaperHeight(): string
	{
		return $this->productCover2PaperHeight;
	}

	/**
	 * @param string $productCover2PaperHeight
	 * @return ProductCollectionLink
	 */
	public function setProductCover2PaperHeight(string $productCover2PaperHeight): ProductCollectionLink
	{
		$this->productCover2PaperHeight = $productCover2PaperHeight;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover2Bleed(): string
	{
		return $this->productCover2Bleed;
	}

	/**
	 * @param string $productCover2Bleed
	 * @return ProductCollectionLink
	 */
	public function setProductCover2Bleed(string $productCover2Bleed): ProductCollectionLink
	{
		$this->productCover2Bleed = $productCover2Bleed;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover2SafeMargin(): string
	{
		return $this->productCover2SafeMargin;
	}

	/**
	 * @param string $productCover2SafeMargin
	 * @return ProductCollectionLink
	 */
	public function setProductCover2SafeMargin(string $productCover2SafeMargin): ProductCollectionLink
	{
		$this->productCover2SafeMargin = $productCover2SafeMargin;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover2Width(): string
	{
		return $this->productCover2Width;
	}

	/**
	 * @param string $productCover2Width
	 * @return ProductCollectionLink
	 */
	public function setProductCover2Width(string $productCover2Width): ProductCollectionLink
	{
		$this->productCover2Width = $productCover2Width;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCover2Height(): string
	{
		return $this->productCover2Height;
	}

	/**
	 * @param string $productCover2Height
	 * @return ProductCollectionLink
	 */
	public function setProductCover2Height(string $productCover2Height): ProductCollectionLink
	{
		$this->productCover2Height = $productCover2Height;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductSelectorModeDesktop(): int
	{
		return $this->productSelectorModeDesktop;
	}

	/**
	 * @param int $productSelectorModeDesktop
	 * @return ProductCollectionLink
	 */
	public function setProductSelectorModeDesktop(int $productSelectorModeDesktop): ProductCollectionLink
	{
		$this->productSelectorModeDesktop = $productSelectorModeDesktop;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductWizardModeOnline(): int
	{
		return $this->productWizardModeOnline;
	}

	/**
	 * @param int $productWizardModeOnline
	 * @return ProductCollectionLink
	 */
	public function setProductWizardModeOnline(int $productWizardModeOnline): ProductCollectionLink
	{
		$this->productWizardModeOnline = $productWizardModeOnline;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductAiModeDesktop(): int
	{
		return $this->productAiModeDesktop;
	}

	/**
	 * @param int $productAiModeDesktop
	 * @return ProductCollectionLink
	 */
	public function setProductAiModeDesktop(int $productAiModeDesktop): ProductCollectionLink
	{
		$this->productAiModeDesktop = $productAiModeDesktop;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductAiModeOnline(): int
	{
		return $this->productAiModeOnline;
	}

	/**
	 * @param int $productAiModeOnline
	 * @return ProductCollectionLink
	 */
	public function setProductAiModeOnline(int $productAiModeOnline): ProductCollectionLink
	{
		$this->productAiModeOnline = $productAiModeOnline;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductCalendarLocale(): string
	{
		return $this->productCalendarLocale;
	}

	/**
	 * @param string $productCalendarLocale
	 * @return ProductCollectionLink
	 */
	public function setProductCalendarLocale(string $productCalendarLocale): ProductCollectionLink
	{
		$this->productCalendarLocale = $productCalendarLocale;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isProductCalendarLocaleCanChange(): bool
	{
		return $this->productCalendarLocaleCanChange;
	}

	/**
	 * @param bool $productCalendarLocaleCanChange
	 * @return ProductCollectionLink
	 */
	public function setProductCalendarLocaleCanChange(bool $productCalendarLocaleCanChange): ProductCollectionLink
	{
		$this->productCalendarLocaleCanChange = $productCalendarLocaleCanChange;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductCalendarStartDay(): int
	{
		return $this->productCalendarStartDay;
	}

	/**
	 * @param int $productCalendarStartDay
	 * @return ProductCollectionLink
	 */
	public function setProductCalendarStartDay(int $productCalendarStartDay): ProductCollectionLink
	{
		$this->productCalendarStartDay = $productCalendarStartDay;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isProductCalendarStartDayCanChange(): bool
	{
		return $this->productCalendarStartDayCanChange;
	}

	/**
	 * @param bool $productCalendarStartDayCanChange
	 * @return ProductCollectionLink
	 */
	public function setProductCalendarStartDayCanChange(bool $productCalendarStartDayCanChange): ProductCollectionLink
	{
		$this->productCalendarStartDayCanChange = $productCalendarStartDayCanChange;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductCalendarStartMonth(): int
	{
		return $this->productCalendarStartMonth;
	}

	/**
	 * @param int $productCalendarStartMonth
	 * @return ProductCollectionLink
	 */
	public function setProductCalendarStartMonth(int $productCalendarStartMonth): ProductCollectionLink
	{
		$this->productCalendarStartMonth = $productCalendarStartMonth;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isProductCalendarStartMonthCanChange(): bool
	{
		return $this->productCalendarStartMonthCanChange;
	}

	/**
	 * @param bool $productCalendarStartMonthCanChange
	 * @return ProductCollectionLink
	 */
	public function setProductCalendarStartMonthCanChange(bool $productCalendarStartMonthCanChange): ProductCollectionLink
	{
		$this->productCalendarStartMonthCanChange = $productCalendarStartMonthCanChange;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductCalendarStartYear(): int
	{
		return $this->productCalendarStartYear;
	}

	/**
	 * @param int $productCalendarStartYear
	 * @return ProductCollectionLink
	 */
	public function setProductCalendarStartYear(int $productCalendarStartYear): ProductCollectionLink
	{
		$this->productCalendarStartYear = $productCalendarStartYear;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isProductCalendarStartYearCanChange(): bool
	{
		return $this->productCalendarStartYearCanChange;
	}

	/**
	 * @param bool $productCalendarStartYearCanChange
	 * @return ProductCollectionLink
	 */
	public function setProductCalendarStartYearCanChange(bool $productCalendarStartYearCanChange): ProductCollectionLink
	{
		$this->productCalendarStartYearCanChange = $productCalendarStartYearCanChange;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductSortOrder(): int
	{
		return $this->productSortOrder;
	}

	/**
	 * @param int $productSortOrder
	 * @return ProductCollectionLink
	 */
	public function setProductSortOrder(int $productSortOrder): ProductCollectionLink
	{
		$this->productSortOrder = $productSortOrder;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductTarget(): int
	{
		return $this->productTarget;
	}

	/**
	 * @param int $productTarget
	 * @return ProductCollectionLink
	 */
	public function setProductTarget(int $productTarget): ProductCollectionLink
	{
		$this->productTarget = $productTarget;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAvailableDesktop(): bool
	{
		return $this->availableDesktop;
	}

	/**
	 * @param bool $availableDesktop
	 * @return ProductCollectionLink
	 */
	public function setAvailableDesktop(bool $availableDesktop): ProductCollectionLink
	{
		$this->availableDesktop = $availableDesktop;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isHasBeenAvailableDesktop(): bool
	{
		return $this->hasBeenAvailableDesktop;
	}

	/**
	 * @param bool $hasBeenAvailableDesktop
	 * @return ProductCollectionLink
	 */
	public function setHasBeenAvailableDesktop(bool $hasBeenAvailableDesktop): ProductCollectionLink
	{
		$this->hasBeenAvailableDesktop = $hasBeenAvailableDesktop;
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
	 * @return ProductCollectionLink
	 */
	public function setAvailableOnline(bool $availableOnline): ProductCollectionLink
	{
		$this->availableOnline = $availableOnline;
		return $this;
	}

    /**
     * @return int
     */
    public function getProductConfigurationFlags(): int
    {
        return $this->productConfigurationFlags;
    }

    /**
     * @param int $productConfigurationFlags
     * @return ProductCollectionLink
     */
    public function setProductConfigurationFlags(int $productConfigurationFlags): ProductCollectionLink
    {
        $this->productConfigurationFlags = $productConfigurationFlags;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductPageContentAssignMode(): int
    {
        return $this->productPageContentAssignMode;
    }

    /**
     * @param int $productPageContentAssignMode
     * @return ProductCollectionLink
     */
    public function setProductPageContentAssignMode(int $productPageContentAssignMode): ProductCollectionLink
    {
        $this->productPageContentAssignMode = $productPageContentAssignMode;
        return $this;
    }

    /**
     * @return string
     */
    public function getCollectionSummary(): string
    {
        return $this->collectionSummary;
    }

    /**
     * @param string $collectionSummary
     * @return ProductCollectionLink
     */
    public function setCollectionSummary(string $collectionSummary): ProductCollectionLink
    {
        $this->collectionSummary = $collectionSummary;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductOrientation(): int
    {
        return $this->productOrientation;
    }

    /**
     * @param int $productOrientation
     * @return ProductCollectionLink
     */
    public function setProductOrientation(int $productOrientation): ProductCollectionLink
    {
        $this->productOrientation = $productOrientation;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductSizeCode(): string
    {
        return $this->productSizeCode;
    }

    /**
     * @param string $productSizeCode
     * @return ProductCollectionLink
     */
    public function setProductSizeCode(string $productSizeCode): ProductCollectionLink
    {
        $this->productSizeCode = $productSizeCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductSizeName(): string
    {
        return $this->productSizeName;
    }

    /**
     * @param string $productSizeName
     * @return ProductCollectionLink
     */
    public function setProductSizeName(string $productSizeName): ProductCollectionLink
    {
        $this->productSizeName = $productSizeName;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductSizeArea(): int
    {
        return $this->productSizeArea;
    }

    /**
     * @param int $productSizeArea
     * @return ProductCollectionLink
     */
    public function setProductSizeArea(int $productSizeArea): ProductCollectionLink
    {
        $this->productSizeArea = $productSizeArea;
        return $this;
    }

    /**
     * @return int
     */
    public function getCollectionThumbnailResourceDevicePixelRatio(): int
    {
        return $this->collectionThumbnailResourceDevicePixelRatio;
    }

    /**
     * @param int $collectionThumbnailResourceDevicePixelRatio
     * @return ProductCollectionLink
     */
    public function setCollectionThumbnailResourceDevicePixelRatio(int $collectionThumbnailResourceDevicePixelRatio): ProductCollectionLink
    {
        $this->collectionThumbnailResourceDevicePixelRatio = $collectionThumbnailResourceDevicePixelRatio;
        return $this;
    }

    /**
     * @return int
     */
    public function getCollectionPreviewResourceDevicePixelRatio(): int
    {
        return $this->collectionPreviewResourceDevicePixelRatio;
    }

    /**
     * @param int $collectionPreviewResourceDevicePixelRatio
     * @return ProductCollectionLink
     */
    public function setCollectionPreviewResourceDevicePixelRatio(int $collectionPreviewResourceDevicePixelRatio): ProductCollectionLink
    {
        $this->collectionPreviewResourceDevicePixelRatio = $collectionPreviewResourceDevicePixelRatio;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductThumbnailResourceDevicePixelRatio(): int
    {
        return $this->productThumbnailResourceDevicePixelRatio;
    }

    /**
     * @param int $productThumbnailResourceDevicePixelRatio
     * @return ProductCollectionLink
     */
    public function setProductThumbnailResourceDevicePixelRatio(int $productThumbnailResourceDevicePixelRatio): ProductCollectionLink
    {
        $this->productThumbnailResourceDevicePixelRatio = $productThumbnailResourceDevicePixelRatio;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductPreviewResourceDevicePixelRatio(): int
    {
        return $this->productPreviewResourceDevicePixelRatio;
    }

    /**
     * @param int $productPreviewResourceDevicePixelRatio
     * @return ProductCollectionLink
     */
    public function setProductPreviewResourceDevicePixelRatio(int $productPreviewResourceDevicePixelRatio): ProductCollectionLink
    {
        $this->productPreviewResourceDevicePixelRatio = $productPreviewResourceDevicePixelRatio;
        return $this;
    }
}
