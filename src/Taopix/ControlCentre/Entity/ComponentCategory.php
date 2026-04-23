<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\ComponentCategoryRepository;

#[ORM\Entity(repositoryClass: ComponentCategoryRepository::class), ORM\Table(name: "componentcategories", schema: "controlcentre")]
#[ORM\UniqueConstraint(name: "SINGULAR", columns: ["code"]), ORM\Index(columns: ["code"], name: "SINGULAR")]
class ComponentCategory
{
	#[ORM\Id, ORM\GeneratedValue, ORM\Column(name: "id", nullable: false)]
	private ?int $id = null;

	#[ORM\Column(name: "datecreated", nullable: false, options: ["default" => "0000-00-00 00:00:00"])]
	private ?DateTime $dateCreated = null;

	#[ORM\Column(name: "companycode", length: 50, nullable: false, options: ["default" => ""])]
	private string $companyCode = '';

	#[ORM\Column(name: "code", length: 50, nullable: false, options: ["default" => ""])]
	private string $code = '';

	#[ORM\Column(name: "name", length: 1024, nullable: false, options: ["default" => ""])]
	private string $name = '';

	#[ORM\Column(name: "prompt", length: 1024, nullable: false, options: ["default" => ""])]
	private string $prompt = '';

	#[ORM\Column(name: "pricingmodel", nullable: false, options: ["default" => 0])]
	private int $pricingModel = 0;

	#[ORM\Column(name: "islist", nullable: false, options: ["default" => false])]
	private bool $list = false;

	#[ORM\Column(name: "requirespagecount", nullable: false, options: ["default" => false])]
	private bool $requiresPageCount = false;

	#[ORM\Column(name: "componentpricingdecimalplaces", nullable: false, options: ["default" => 2])]
	private int $decimalPlaces = 2;

	#[ORM\Column(name: "private", nullable: false, options: ["default" => false])]
	private bool $private = false;

	#[ORM\Column(name: "active", nullable: false, options: ["default" => false])]
	private bool $active = false;

	#[ORM\Column(name: "displaystage", nullable: false, options: ["default" => 2])]
	private int $displayStage = 2;

	#[ORM\Column(name: "deleted", nullable: false, options: ["default" => false])]
	private bool $deleted = false;

	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @param int|null $id
	 * @return ComponentCategory
	 */
	public function setId(?int $id): ComponentCategory
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
	 * @return ComponentCategory
	 */
	public function setDateCreated(?DateTime $dateCreated): ComponentCategory
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
	 * @return ComponentCategory
	 */
	public function setCompanyCode(string $companyCode): ComponentCategory
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
	 * @return ComponentCategory
	 */
	public function setCode(string $code): ComponentCategory
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
	 * @return ComponentCategory
	 */
	public function setName(string $name): ComponentCategory
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrompt(): string
	{
		return $this->prompt;
	}

	/**
	 * @param string $prompt
	 * @return ComponentCategory
	 */
	public function setPrompt(string $prompt): ComponentCategory
	{
		$this->prompt = $prompt;
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
	 * @return ComponentCategory
	 */
	public function setPricingModel(int $pricingModel): ComponentCategory
	{
		$this->pricingModel = $pricingModel;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isList(): bool
	{
		return $this->list;
	}

	/**
	 * @param bool $list
	 * @return ComponentCategory
	 */
	public function setList(bool $list): ComponentCategory
	{
		$this->list = $list;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isRequiresPageCount(): bool
	{
		return $this->requiresPageCount;
	}

	/**
	 * @param bool $requiresPageCount
	 * @return ComponentCategory
	 */
	public function setRequiresPageCount(bool $requiresPageCount): ComponentCategory
	{
		$this->requiresPageCount = $requiresPageCount;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getDecimalPlaces(): int
	{
		return $this->decimalPlaces;
	}

	/**
	 * @param int $decimalPlaces
	 * @return ComponentCategory
	 */
	public function setDecimalPlaces(int $decimalPlaces): ComponentCategory
	{
		$this->decimalPlaces = $decimalPlaces;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isPrivate(): bool
	{
		return $this->private;
	}

	/**
	 * @param bool $private
	 * @return ComponentCategory
	 */
	public function setPrivate(bool $private): ComponentCategory
	{
		$this->private = $private;
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
	 * @return ComponentCategory
	 */
	public function setActive(bool $active): ComponentCategory
	{
		$this->active = $active;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getDisplayStage(): int
	{
		return $this->displayStage;
	}

	/**
	 * @param int $displayStage
	 * @return ComponentCategory
	 */
	public function setDisplayStage(int $displayStage): ComponentCategory
	{
		$this->displayStage = $displayStage;
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
	 * @return ComponentCategory
	 */
	public function setDeleted(bool $deleted): ComponentCategory
	{
		$this->deleted = $deleted;
		return $this;
	}
}