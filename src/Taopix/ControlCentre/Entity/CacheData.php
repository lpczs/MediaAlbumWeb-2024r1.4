<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\CacheDataRepository;

#[ORM\Entity(repositoryClass: CacheDataRepository::class), ORM\Table(name: "cachedata", schema: "controlcentre")]
class CacheData
{
	#[ORM\Id, ORM\Column(name: "datacachekey", length: 256, nullable: false)]
	private string $dataCacheKey = '';

	#[ORM\Column(name: "datecreated", nullable: false)]
	private ?DateTime $dateCreated = null;

	#[ORM\Column(name: "groupcode", length: 50, nullable: false)]
	private string $groupCode = '';

	#[ORM\Column(name: "companycode", length: 50, nullable: false)]
	private string $companyCode = '';

	#[ORM\Column(name: "cachedata", type: Types::BLOB, length: AbstractMySQLPlatform::LENGTH_LIMIT_MEDIUMBLOB, nullable: false)]
	private mixed $cacheData = null;

	#[ORM\Column(name: "serializeddatalength", nullable: false)]
	private int $serializedDataLength = 0;

	#[ORM\Column(name: "cacheversion", length: 30, nullable: false)]
	private string $cacheVersion = '';

	private array $cacheArray = [];

	/**
	 * @return string
	 */
	public function getDataCacheKey(): string
	{
		return $this->dataCacheKey;
	}

	/**
	 * @param string $dataCacheKey
	 * @return self
	 */
	public function setDataCacheKey(string $dataCacheKey): self
	{
		$this->dataCacheKey = $dataCacheKey;
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
	public function getGroupCode(): string
	{
		return $this->groupCode;
	}

	/**
	 * @param string $groupCode
	 * @return self
	 */
	public function setGroupCode(string $groupCode): self
	{
		$this->groupCode = $groupCode;
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
	 * @return self
	 */
	public function setCompanyCode(string $companyCode): self
	{
		$this->companyCode = $companyCode;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCacheData(): array|null
	{
		return $this->cacheData;
	}

	/**
	 * @param mixed $cacheData
	 * @return self
	 */
	public function setCacheData(mixed $cacheData): self
	{
		$this->cacheData = $cacheData;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSerializedDataLength(): int
	{
		return $this->serializedDataLength;
	}

	/**
	 * @param int $serializedDataLength
	 * @return self
	 */
	public function setSerializedDataLength(int $serializedDataLength): self
	{
		$this->serializedDataLength = $serializedDataLength;
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
	 * @return self
	 */
	public function setCacheVersion(string $cacheVersion): self
	{
		$this->cacheVersion = $cacheVersion;
		return $this;
	}

	/**
	 * Converts the internal serialized data to an array.
	 *
	 * @return array
	 */
	public function getCacheArray(): array
	{
		if (!empty($this->cacheArray))
		{
			return $this->cacheArray;
		}

		$this->cacheArray = match(true) {
			49152 > $this->serializedDataLength => unserialize(stream_get_contents($this->cacheData)),
			default => unserialize(gzuncompress(stream_get_contents($this->cacheData), $this->serializedDataLength)),
		};

		return $this->cacheArray;
	}

	/**
	 * Converts a price array to an internal serialized value.
	 * This may get compressed if the data is large enough.
	 *
	 * @param array $data
	 * @return $this
	 */
	public function setCacheArray(array $data): self
	{
		$this->cacheArray = $data;
		$serialized = serialize($data);
		$serializedLength = strlen($serialized);

		if (49152 < $serializedLength) {
			$serialized = gzcompress($serialized, 9);
		}

		$this->cacheData = $serialized;
		$this->serializedDataLength = $serializedLength;

		return $this;
	}
}
