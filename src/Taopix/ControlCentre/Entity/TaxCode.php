<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\TaxCodeRepository;

#[ORM\Entity(repositoryClass: TaxCodeRepository::class), ORM\Table(name: "taxrates", schema: "controlcentre"), ORM\UniqueConstraint(name: "code", columns: ["code"])]
#[ORM\Index(columns: ["code"], name: "code")]
class TaxCode
{
	#[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO'), ORM\Column(name: "id", nullable: false)]
	private ?int $id = null;

	#[ORM\Column(name: "datecreated", nullable: false)]
	private ?DateTime $dateCreated = null;

	#[ORM\Column(name: "code", length: 50, nullable: false)]
	private string $code = '';

	#[ORM\Column(name: "name", length: 1024, nullable: false)]
	private string $name = '';

	#[ORM\Column(name: "rate", type: Types::DECIMAL, precision: 10, scale: 4, nullable: false)]
	private string $rate = '0.0000';

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
	public function getRate(): string
	{
		return $this->rate;
	}

	/**
	 * @param string $rate
	 * @return self
	 */
	public function setRate(string $rate): self
	{
		$this->rate = $rate;
		return $this;
	}
}