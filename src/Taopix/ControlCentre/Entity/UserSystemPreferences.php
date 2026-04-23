<?php

namespace Taopix\ControlCentre\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\TaxZoneRepository;
use Taopix\ControlCentre\Traits\Entity\ToArrayTrait;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: TaxZoneRepository::class), ORM\Table(name: "usersystempreferences", schema: "controlcentre")]
class UserSystemPreferences
{
	use ToArrayTrait;

	#[ORM\Id, ORM\GeneratedValue, ORM\Column(name: "id", nullable: false)]
	private ?int $id = null;
	
	#[ORM\Column(name: "type", length: 50, nullable: false)]
	private string $type = '';

	#[ORM\Column(name: "userid", length: 100, type: Types::INTEGER, nullable: false)]
	private int $userId = 0;

	#[ORM\Column(name: "data", type: Types::BLOB)]
	private mixed $data = null;

	#[ORM\Column(name: "datalength", type: Types::INTEGER, nullable: false)]
	private int $dataLength = 0;

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
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 * @return UserSystemPreferences
	 */
	public function setType(string $type): UserSystemPreferences
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getUserId(): int
	{
		return $this->userId;
	}

	/**
	 * @param int $id
	 * @return UserSystemPreferences
	 */
	public function setUserId(int $userId): UserSystemPreferences
	{
		$this->userId = $userId;
		return $this;
	}

	/**
     * @return ?string
     */
    public function getData(): ?string
    {
        return match(gettype($this->data)) {
            'resource' => stream_get_contents($this->data),
            default => $this->data,
        };
    }

    /**
     * @param string $data
     * @return UserSystemPreferences
     */
    public function setData(string $data): UserSystemPreferences
    {
        $this->data = $data;
        return $this;
    }

	/**
	 * @return int
	 */
	public function getDataLength(): int
	{
		return $this->dataLength;
	}

	/**
	 * @param int $dataLength
	 * @return UserSystemPreferences
	 */
	public function setDataLength(int $dataLength): UserSystemPreferences
	{
		$this->dataLength = $dataLength;
		return $this;
	}

	/**
     * Populates an entity class from the passed array.
     *
     * @param array $details associative array containing the details of the entity we wish to populate
     */
    public function populate(array $details): self
    {
        foreach ($details as $key => $value) {
            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }

        return $this;
    }
}