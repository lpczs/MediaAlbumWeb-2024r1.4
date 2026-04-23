<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\ApplicationFileRepository;
use Taopix\ControlCentre\Traits\Entity\ToArrayTrait;

#[ORM\Entity(repositoryClass: ApplicationFileRepository::class), ORM\Table(name: "applicationfiles", schema: "controlcentre")]
#[ORM\UniqueConstraint(name: "id", columns: ["id"]), ORM\Index(columns: ["ref", "type", "deleted"], name: "reftypedeleted")]

class ApplicationFile
{
	use ToArrayTrait;

	#[ORM\Id, ORM\GeneratedValue, ORM\Column(name: "id", type: "integer", nullable:false)]
	private ?int $id = null;

	#[ORM\Column(name: "datecreated", type: "datetime", options: ["default" => "0000-00-00 00:00:00"])]
	private ?DateTime $dateCreated = null;

	#[ORM\Column(name: "companycode", type:"string", length: 50, nullable: false)]
	private string $companyCode = '';

	#[ORM\Column(name: "type", type: "integer", nullable: false)]
	private int $type = 0;

	#[ORM\Column(name: "ref", type: "string", length: 255, nullable: false)]
	private string $ref = '';

	#[ORM\Column(name: "appversion", type: "string", length: 20, nullable: false)]
	private string $appVersion = '';

	#[ORM\Column(name: "dataversion", type: "integer", nullable: false)]
	private int $dataVersion = 0;

	#[ORM\Column(name: "categorycode", type: "string", length: 50, nullable: false)]
	private string $categoryCode = '';

	#[ORM\Column(name: "categoryname", type: "string", length: 1024, nullable: false)]
	private string $categoryName = '';

	#[ORM\Column(name: "name", type: "string", length: 1024, nullable: false)]
	private string $name = '';

	#[ORM\Column(name: "description", type: "string", length: 1024, nullable: false)]
	private string $description = '';

	#[ORM\Column(name: "products", type: "string", length: 4096, nullable: false)]
	private string $products = '';

	#[ORM\Column(name: "themes", type: "string", length: 1024, nullable: false)]
	private string $themes = '';

	#[ORM\Column(name: "filename", type: "string", length: 255, nullable: false)]
	private string $fileName = '';

	#[ORM\Column(name: "versiondate", type: "datetime", nullable: false, options: ["default" => "0000-00-00 00:00:00"])]
	private ?DateTime $versionDate = null;

	#[ORM\Column(name: "versiondateonline", type: "datetime", nullable: false, options: ["default" => '0000-00-00 00:00:00'])]
	private ?DateTime $versionDateOnline = null;

	#[ORM\Column(name: "encrypted", type: "boolean", nullable: false)]
	private bool $encrypted = false;

	#[ORM\Column(name: "updatepriority", type: "integer", nullable: false)]
	private int $updatePriority = 0;

	#[ORM\Column(name: "dependencies", type: "text", length: 16777215, nullable: false)]
	private string $dependencies = '';

	#[ORM\Column(name: "onlinedependencies", type: "text", length: 16777215, nullable: false)]
	private $onlineDependencies = '';

	#[ORM\Column(name: "size", type: "integer", nullable: false, options: ["unsigned" => true])]
	private int $size = 0;

	#[ORM\Column(name: "checksum", type: "string", length: 255, nullable: false)]
	private string $checkSum = '';

	#[ORM\Column(name: "hasfpo", type: "boolean", nullable: false)]
	private bool $hasFPO = false;

	#[ORM\Column(name: "haspreview", type: "boolean", nullable: false)]
	private bool $hasPreview = false;

	#[ORM\Column(name: "separatecomponents", type: "boolean", nullable: false)]
	private bool $separateComponents = false;

	#[ORM\Column(name: "hiddenfromuser", type: "boolean", nullable: false)]
	private bool $hiddenFromUser = false;

	#[ORM\Column(name: "hasdesktoplayouts", type: "boolean", nullable: false)]
	private bool $hasDesktopLayouts = false;

	#[ORM\Column(name: "hasonlinelayouts", type: "boolean", nullable: false)]
	private bool $hasOnlineLayouts = false;

	#[ORM\Column(name: "webbrandcode", type: "string", length: 50, nullable: false)]
	private string $webBrandCode = '';

	#[ORM\Column(name: "active", type: "boolean", nullable: false)]
	private bool $active = false;

	#[ORM\Column(name: "onlineactive", type: "boolean", nullable: false)]
	private bool $onlineActive = false;

	#[ORM\Column(name: "deleted", type: "boolean", nullable: false)]
	private bool $deleted = false;

	/**
	 * @return int
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return ApplicationFile
	 */
	public function setId(int $id): self
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getDateCreated(): ?DateTime
	{
		return $this->dateCreated;
	}

	/**
	 * @param DateTime $dateCreated
	 * @return ApplicationFile
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
	 * @return ApplicationFile
	 */
	public function setCompanyCode(string $companyCode): self
	{
		$this->companyCode = $companyCode;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getType(): int
	{
		return $this->type;
	}

	/**
	 * @param int $type
	 * @return ApplicationFile
	 */
	public function setType(int $type): self
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRef(): string
	{
		return $this->ref;
	}

	/**
	 * @param string $ref
	 * @return ApplicationFile
	 */
	public function setRef(string $ref): self
	{
		$this->ref = $ref;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAppVersion(): string
	{
		return $this->appVersion;
	}

	/**
	 * @param string $appVersion
	 * @return ApplicationFile
	 */
	public function setAppVersion(string $appVersion): self
	{
		$this->appVersion = $appVersion;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getDataVersion(): int
	{
		return $this->dataVersion;
	}

	/**
	 * @param int $dataVersion
	 * @return ApplicationFile
	 */
	public function setDataVersion(int $dataVersion): self
	{
		$this->dataVersion = $dataVersion;
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
	 * @return ApplicationFile
	 */
	public function setCategoryCode(string $categoryCode): self
	{
		$this->categoryCode = $categoryCode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCategoryName(): string
	{
		return $this->categoryName;
	}

	/**
	 * @param string $categoryName
	 * @return ApplicationFile
	 */
	public function setCategoryName(string $categoryName): self
	{
		$this->categoryName = $categoryName;
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
	 * @return ApplicationFile
	 */
	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return ApplicationFile
	 */
	public function setDescription(string $description): self
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProducts(): string
	{
		return $this->products;
	}

	/**
	 * @param string $products
	 * @return ApplicationFile
	 */
	public function setProducts(string $products): self
	{
		$this->products = $products;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getThemes(): string
	{
		return $this->themes;
	}

	/**
	 * @param string $themes
	 * @return ApplicationFile
	 */
	public function setThemes(string $themes): self
	{
		$this->themes = $themes;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFileName(): string
	{
		return $this->fileName;
	}

	/**
	 * @param string $fileName
	 * @return ApplicationFile
	 */
	public function setFileName(string $fileName): self
	{
		$this->fileName = $fileName;
		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getVersionDate(): ?DateTime
	{
		return $this->versionDate;
	}

	/**
	 * @param DateTime $versionDate
	 * @return ApplicationFile
	 */
	public function setVersionDate(DateTime $versionDate): self
	{
		$this->versionDate = $versionDate;
		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getVersionDateOnline(): ?DateTime
	{
		return $this->versionDateOnline;
	}

	/**
	 * @param DateTime $versionDateOnline
	 * @return ApplicationFile
	 */
	public function setVersionDateOnline(DateTime $versionDateOnline): self
	{
		$this->versionDateOnline = $versionDateOnline;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isEncrypted(): bool
	{
		return $this->encrypted;
	}

	/**
	 * @param bool $encrypted
	 * @return ApplicationFile
	 */
	public function setEncrypted(bool $encrypted): self
	{
		$this->encrypted = $encrypted;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getUpdatePriority(): int
	{
		return $this->updatePriority;
	}

	/**
	 * @param int $updatePriority
	 * @return ApplicationFile
	 */
	public function setUpdatePriority(int $updatePriority): self
	{
		$this->updatePriority = $updatePriority;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDependencies(): string
	{
		return $this->dependencies;
	}

	/**
	 * @param string $dependencies
	 * @return ApplicationFile
	 */
	public function setDependencies(string $dependencies): self
	{
		$this->dependencies = $dependencies;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOnlineDependencies(): string
	{
		return $this->onlineDependencies;
	}

	/**
	 * @param string $onlineDependencies
	 * @return ApplicationFile
	 */
	public function setOnlineDependencies(string $onlineDependencies): self
	{
		$this->onlineDependencies = $onlineDependencies;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSize(): int
	{
		return $this->size;
	}

	/**
	 * @param int $size
	 * @return ApplicationFile
	 */
	public function setSize(int $size): self
	{
		$this->size = $size;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCheckSum(): string
	{
		return $this->checkSum;
	}

	/**
	 * @param string $checkSum
	 * @return ApplicationFile
	 */
	public function setCheckSum(string $checkSum): self
	{
		$this->checkSum = $checkSum;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isHasFPO(): bool
	{
		return $this->hasFPO;
	}

	/**
	 * @param bool $hasFPO
	 * @return ApplicationFile
	 */
	public function setHasFPO(bool $hasFPO): self
	{
		$this->hasFPO = $hasFPO;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isHasPreview(): bool
	{
		return $this->hasPreview;
	}

	/**
	 * @param bool $hasPreview
	 * @return ApplicationFile
	 */
	public function setHasPreview(bool $hasPreview): self
	{
		$this->hasPreview = $hasPreview;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSeparateComponents(): bool
	{
		return $this->separateComponents;
	}

	/**
	 * @param bool $separateComponents
	 * @return ApplicationFile
	 */
	public function setSeparateComponents(bool $separateComponents): self
	{
		$this->separateComponents = $separateComponents;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isHiddenFromUser(): bool
	{
		return $this->hiddenFromUser;
	}

	/**
	 * @param bool $hiddenFromUser
	 * @return ApplicationFile
	 */
	public function setHiddenFromUser(bool $hiddenFromUser): self
	{
		$this->hiddenFromUser = $hiddenFromUser;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isHasDesktopLayouts(): bool
	{
		return $this->hasDesktopLayouts;
	}

	/**
	 * @param bool $hasDesktopLayouts
	 * @return ApplicationFile
	 */
	public function setHasDesktopLayouts(bool $hasDesktopLayouts): self
	{
		$this->hasDesktopLayouts = $hasDesktopLayouts;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isHasOnlineLayouts(): bool
	{
		return $this->hasOnlineLayouts;
	}

	/**
	 * @param bool $hasOnlineLayouts
	 * @return ApplicationFile
	 */
	public function setHasOnlineLayouts(bool $hasOnlineLayouts): self
	{
		$this->hasOnlineLayouts = $hasOnlineLayouts;
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
	 * @return ApplicationFile
	 */
	public function setWebBrandCode(string $webBrandCode): self
	{
		$this->webBrandCode = $webBrandCode;
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
	 * @return ApplicationFile
	 */
	public function setActive(bool $active): self
	{
		$this->active = $active;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isOnlineActive(): bool
	{
		return $this->onlineActive;
	}

	/**
	 * @param bool $onlineActive
	 * @return ApplicationFile
	 */
	public function setOnlineActive(bool $onlineActive): self
	{
		$this->onlineActive = $onlineActive;
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
	 * @return ApplicationFile
	 */
	public function setDeleted(bool $deleted): self
	{
		$this->deleted = $deleted;
		return $this;
	}

}
