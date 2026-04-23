<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Taopix\ControlCentre\Repository\CurrencyRepository;

#[ORM\Entity(repositoryClass: CurrencyRepository::class), ORM\Table(name: "currencies", schema: "controlcentre")]
#[ORM\UniqueConstraint(name: "code", columns: ["code"]), ORM\UniqueConstraint(name: "isonumber", columns: ["isonumber"])]
#[ORM\Index(columns: ["code"], name: "code"), ORM\Index(columns: ["isonumber"], name: "isonumber")]
class Currency
{
	#[ORM\Id, ORM\GeneratedValue(strategy: "AUTO"), ORM\Column(name: "id", nullable: false)]
	private ?int $id = null;

	#[ORM\Column(name: "datecreated", nullable: false)]
	private ?DateTime $dateCreated = null;

	#[ORM\Column(name: "code", length: 20, nullable: false), Groups(['currency-details'])]
	private string $code = '';

	#[ORM\Column(name: "name", length: 1024, nullable: false), Groups(['currency-details'])]
	private string $name = '';

	#[ORM\Column(name: "isonumber", length: 3, nullable: false), Groups(['currency-details'])]
	private string $isoNumber = '';

	#[ORM\Column(name: "symbol", length: 5, nullable: false), Groups(['currency-details'])]
	private string $symbol = '';

	#[ORM\Column(name: "symbolatfront", nullable: false), Groups(['currency-details'])]
	private bool $symbolAtFront = true;

	#[ORM\Column(name: "decimalplaces", nullable: false), Groups(['currency-details'])]
	private int $decimalPlaces = 2;

	#[ORM\Column(name: "exchangeratedateset", nullable: false)]
	private ?DateTime $exchangeRateDateSet = null;

	#[ORM\Column(name: "exchangerate", type: Types::DECIMAL, precision: 10, scale: 4, nullable: false), Groups(['currency-details'])]
	private string $exchangeRate = '1.0000';

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
	public function getIsoNumber(): string
	{
		return $this->isoNumber;
	}

	/**
	 * @param string $isoNumber
	 * @return self
	 */
	public function setIsoNumber(string $isoNumber): self
	{
		$this->isoNumber = $isoNumber;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSymbol(): string
	{
		return $this->symbol;
	}

	/**
	 * @param string $symbol
	 * @return self
	 */
	public function setSymbol(string $symbol): self
	{
		$this->symbol = $symbol;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSymbolAtFront(): bool
	{
		return $this->symbolAtFront;
	}

	/**
	 * @param bool $symbolAtFront
	 * @return self
	 */
	public function setSymbolAtFront(bool $symbolAtFront): self
	{
		$this->symbolAtFront = $symbolAtFront;
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
	 * @return self
	 */
	public function setDecimalPlaces(int $decimalPlaces): self
	{
		$this->decimalPlaces = $decimalPlaces;
		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getExchangeRateDateSet(): ?DateTime
	{
		return $this->exchangeRateDateSet;
	}

	/**
	 * @param DateTime|null $exchangeRateDateSet
	 * @return self
	 */
	public function setExchangeRateDateSet(?DateTime $exchangeRateDateSet): self
	{
		$this->exchangeRateDateSet = $exchangeRateDateSet;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getExchangeRate(): string
	{
		return $this->exchangeRate;
	}

	/**
	 * @param string $exchangeRate
	 * @return self
	 */
	public function setExchangeRate(string $exchangeRate): self
	{
		$this->exchangeRate = $exchangeRate;
		return $this;
	}
}
