<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\ProductRepository;
use Taopix\ControlCentre\Traits\Entity\ToArrayTrait;

#[ORM\Table(name: "products", schema: "controlcentre"), ORM\UniqueConstraint(name: "code", columns: ["code"])]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
	use ToArrayTrait;

	#[ORM\Id, ORM\GeneratedValue, ORM\Column(name: "id", nullable: false)]
    private ?int $id = null;

	#[ORM\Column(name: "datecreated", nullable: false)]
    private ?DateTime $dateCreated = null;

	#[ORM\Column(name: "companycode", length: 50, nullable: false)]
    private string $companyCode = '';

	#[ORM\Column(name: "code", length: 50, nullable: false)]
    private string $code = '';

	#[ORM\Column(name: "skucode", length: 100, nullable: false)]
    private string $skuCode = '';

	#[ORM\Column(name: "name", length: 1024, nullable: false)]
    private string $name = '';

	#[ORM\Column(name: "taxlevel", nullable: false)]
    private int $taxLevel = 1;

	#[ORM\Column(name: "unitcost", type: Types::DECIMAL, precision: 10, scale: 2, nullable: false)]
    private string $unitcost = '0.00';

	#[ORM\Column(name: "weight", type: Types::DECIMAL, precision: 10, scale: 4, nullable: false)]
    private string $weight = '0.0000';

	#[ORM\Column(name: "jobticketfield1name", length: 100, nullable: false)]
    private string $jobTicketField1Name = '';

	#[ORM\Column(name: "jobticketfield1value", length: 200, nullable: false)]
    private string $jobTicketField1Value = '';

	#[ORM\Column(name: "jobticketfield2name", length: 100, nullable: false)]
    private string $jobTicketField2Name = '';

	#[ORM\Column(name: "jobticketfield2value", length: 200, nullable: false)]
    private string $jobTicketField2Value = '';

	#[ORM\Column(name: "jobticketfield3name", length: 100, nullable: false)]
    private string $jobTicketField3Name = '';

	#[ORM\Column(name: "jobticketfield3value", length: 200, nullable: false)]
    private string $jobTicketField3Value = '';

	#[ORM\Column(name: "jobticketfield4name", length: 100, nullable: false)]
    private string $jobTicketField4Name = '';

	#[ORM\Column(name: "jobticketfield4value", length: 200, nullable: false)]
    private string $jobTicketField4Value = '';

	#[ORM\Column(name: "jobticketfield5name", length: 100, nullable: false)]
    private string $jobTicketField5Name = '';

	#[ORM\Column(name: "jobticketfield5value", length: 200, nullable: false)]
    private string $jobTicketField5Value = '';

	#[ORM\Column(name: "createnewprojects", nullable: false)]
    private bool $createNewProjects = true;

	#[ORM\Column(name: "previewtype", nullable: false)]
    private int $previewType = 1;

	#[ORM\Column(name: "previewcovertype", type: Types::SMALLINT, nullable: false)]
    private int $previewCoverType = 0;

	#[ORM\Column(name: "previewautoflip", nullable: false)]
    private bool $previewAutoFlip = false;

	#[ORM\Column(name: "previewthumbnailsview", nullable: false)]
    private bool $previewThumbnailsView = true;

	#[ORM\Column(name: "previewthumbnails", nullable: false)]
    private bool $previewThumbnails = true;

	#[ORM\Column(name: "productoptions", type: Types::SMALLINT, nullable: false)]
    private int $productOptions = 0;

	#[ORM\Column(name: "pricetransformationstage", type: Types::SMALLINT, nullable: false)]
    private int $priceTransformationStage = 2;

	#[ORM\Column(name: "minimumprintsperproject", nullable: false, options: ["unsigned" => true])]
    private int $minimumPrintsPerProject = 1;

	#[ORM\Column(name: "usedefaultimagescalingbefore", nullable: false)]
    private bool $useDefaultImageScalingBefore = true;

	#[ORM\Column(name: "imagescalingbeforeenabled", nullable: false)]
    private bool $imageScalingBeforeEnabled = false;

	#[ORM\Column(name: "imagescalingbefore", type: Types::DECIMAL, precision: 5, scale: 2, nullable: false)]
    private string $imageScalingBefore = '0.00';

	#[ORM\Column(name: "usedefaultaveragepicturesperpage", nullable: false)]
    private bool $useDefaultAveragePicturesPerPage = false;

	#[ORM\Column(name: "averagepicturesperpage", type: Types::SMALLINT, nullable: false)]
    private int $averagePicturesPerPage = 0;

	#[ORM\Column(name: "active", nullable: false)]
    private bool $active = false;

	#[ORM\Column(name: "retroprints", nullable: false)]
    private bool $retroPrints = false;

	#[ORM\Column(name: "deleted", nullable: false)]
    private bool $deleted = false;

	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return Product
	 */
	public function setId(int $id): self
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
	 * @return Product
	 */
	public function setDateCreated(DateTime $dateCreated): self
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
	 * @return Product
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
	 * @return Product
	 */
	public function setCode(string $code): self
	{
		$this->code = $code;
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
	 * @return Product
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
	 * @return Product
	 */
	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getTaxLevel(): int
	{
		return $this->taxLevel;
	}

	/**
	 * @param int $taxLevel
	 * @return Product
	 */
	public function setTaxLevel(int $taxLevel): self
	{
		$this->taxLevel = $taxLevel;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUnitcost(): string
	{
		return $this->unitcost;
	}

	/**
	 * @param string $unitcost
	 * @return Product
	 */
	public function setUnitcost(string $unitcost): self
	{
		$this->unitcost = $unitcost;
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
	 * @return Product
	 */
	public function setWeight(string $weight): self
	{
		$this->weight = $weight;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getAssetId(): int
	{
		return $this->assetId;
	}

	/**
	 * @param int $assetId
	 * @return Product
	 */
	public function setAssetId(int $assetId): self
	{
		$this->assetId = $assetId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getJobTicketField1Name(): string
	{
		return $this->jobTicketField1Name;
	}

	/**
	 * @param string $jobTicketField1Name
	 * @return Product
	 */
	public function setJobTicketField1Name(string $jobTicketField1Name): self
	{
		$this->jobTicketField1Name = $jobTicketField1Name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getJobTicketField1Value(): string
	{
		return $this->jobTicketField1Value;
	}

	/**
	 * @param string $jobTicketField1Value
	 * @return Product
	 */
	public function setJobTicketField1Value(string $jobTicketField1Value): self
	{
		$this->jobTicketField1Value = $jobTicketField1Value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getJobTicketField2Name(): string
	{
		return $this->jobTicketField2Name;
	}

	/**
	 * @param string $jobTicketField2Name
	 * @return Product
	 */
	public function setJobTicketField2Name(string $jobTicketField2Name): self
	{
		$this->jobTicketField2Name = $jobTicketField2Name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getJobTicketField2Value(): string
	{
		return $this->jobTicketField2Value;
	}

	/**
	 * @param string $jobTicketField2Value
	 * @return Product
	 */
	public function setJobTicketField2Value(string $jobTicketField2Value): self
	{
		$this->jobTicketField2Value = $jobTicketField2Value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getJobTicketField3Name(): string
	{
		return $this->jobTicketField3Name;
	}

	/**
	 * @param string $jobTicketField3Name
	 * @return Product
	 */
	public function setJobTicketField3Name(string $jobTicketField3Name): self
	{
		$this->jobTicketField3Name = $jobTicketField3Name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getJobTicketField3Value(): string
	{
		return $this->jobTicketField3Value;
	}

	/**
	 * @param string $jobTicketField3Value
	 * @return Product
	 */
	public function setJobTicketField3Value(string $jobTicketField3Value): self
	{
		$this->jobTicketField3Value = $jobTicketField3Value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getJobTicketField4Name(): string
	{
		return $this->jobTicketField4Name;
	}

	/**
	 * @param string $jobTicketField4Name
	 * @return Product
	 */
	public function setJobTicketField4Name(string $jobTicketField4Name): self
	{
		$this->jobTicketField4Name = $jobTicketField4Name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getJobTicketField4Value(): string
	{
		return $this->jobTicketField4Value;
	}

	/**
	 * @param string $jobTicketField4Value
	 * @return Product
	 */
	public function setJobTicketField4Value(string $jobTicketField4Value): self
	{
		$this->jobTicketField4Value = $jobTicketField4Value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getJobTicketField5Name(): string
	{
		return $this->jobTicketField5Name;
	}

	/**
	 * @param string $jobTicketField5Name
	 * @return Product
	 */
	public function setJobTicketField5Name(string $jobTicketField5Name): self
	{
		$this->jobTicketField5Name = $jobTicketField5Name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getJobTicketField5Value(): string
	{
		return $this->jobTicketField5Value;
	}

	/**
	 * @param string $jobTicketField5Value
	 * @return Product
	 */
	public function setJobTicketField5Value(string $jobTicketField5Value): self
	{
		$this->jobTicketField5Value = $jobTicketField5Value;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isCreateNewProjects(): bool
	{
		return $this->createNewProjects;
	}

	/**
	 * @param bool $createNewProjects
	 * @return Product
	 */
	public function setCreateNewProjects(bool $createNewProjects): self
	{
		$this->createNewProjects = $createNewProjects;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPreviewType(): int
	{
		return $this->previewType;
	}

	/**
	 * @param int $previewType
	 * @return Product
	 */
	public function setPreviewType(int $previewType): self
	{
		$this->previewType = $previewType;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPreviewCoverType(): int
	{
		return $this->previewCoverType;
	}

	/**
	 * @param int $previewCoverType
	 * @return Product
	 */
	public function setPreviewCoverType(int $previewCoverType): self
	{
		$this->previewCoverType = $previewCoverType;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isPreviewAutoFlip(): bool
	{
		return $this->previewAutoFlip;
	}

	/**
	 * @param bool $previewAutoFlip
	 * @return Product
	 */
	public function setPreviewAutoFlip(bool $previewAutoFlip): self
	{
		$this->previewAutoFlip = $previewAutoFlip;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isPreviewThumbnailsView(): bool
	{
		return $this->previewThumbnailsView;
	}

	/**
	 * @param bool $previewThumbnailsView
	 * @return Product
	 */
	public function setPreviewThumbnailsView(bool $previewThumbnailsView): self
	{
		$this->previewThumbnailsView = $previewThumbnailsView;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isPreviewThumbnails(): bool
	{
		return $this->previewThumbnails;
	}

	/**
	 * @param bool $previewThumbnails
	 * @return Product
	 */
	public function setPreviewThumbnails(bool $previewThumbnails): self
	{
		$this->previewThumbnails = $previewThumbnails;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getProductOptions(): int
	{
		return $this->productOptions;
	}

	/**
	 * @param int $productOptions
	 * @return Product
	 */
	public function setProductOptions(int $productOptions): self
	{
		$this->productOptions = $productOptions;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPriceTransformationStage(): int
	{
		return $this->priceTransformationStage;
	}

	/**
	 * @param int $priceTransformationStage
	 * @return Product
	 */
	public function setPriceTransformationStage(int $priceTransformationStage): self
	{
		$this->priceTransformationStage = $priceTransformationStage;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getMinimumPrintsPerProject(): int
	{
		return $this->minimumPrintsPerProject;
	}

	/**
	 * @param int $minimumPrintsPerProject
	 * @return Product
	 */
	public function setMinimumPrintsPerProject(int $minimumPrintsPerProject): self
	{
		$this->minimumPrintsPerProject = $minimumPrintsPerProject;
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
	 * @return Product
	 */
	public function setUseDefaultImageScalingBefore(bool $useDefaultImageScalingBefore): self
	{
		$this->useDefaultImageScalingBefore = $useDefaultImageScalingBefore;
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
	 * @return Product
	 */
	public function setImageScalingBeforeEnabled(bool $imageScalingBeforeEnabled): self
	{
		$this->imageScalingBeforeEnabled = $imageScalingBeforeEnabled;
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
	 * @return Product
	 */
	public function setUseDefaultAveragePicturesPerPage(bool $useDefaultAveragePicturesPerPage): self
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
	 * @return Product
	 */
	public function setAveragePicturesPerPage(int $averagePicturesPerPage): self
	{
		$this->averagePicturesPerPage = $averagePicturesPerPage;
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
	 * @return Product
	 */
	public function setImageScalingBefore(string $imageScalingBefore): self
	{
		$this->imageScalingBefore = $imageScalingBefore;
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
	 * @return Product
	 */
	public function setActive(bool $active): self
	{
		$this->active = $active;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isRetroPrints(): bool
	{
		return $this->retroPrints;
	}

	/**
	 * @param bool $deleted
	 * @return Product
	 */
	public function setRetroPrints(bool $retroPrints): self
	{
		$this->retroPrints = $retroPrints;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isDeleted(): bool
	{
		return $this->deleted;
	}

	/**
	 * @param bool $deleted
	 * @return Product
	 */
	public function setDeleted(bool $deleted): self
	{
		$this->deleted = $deleted;
		return $this;
	}
}
