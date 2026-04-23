<?php

namespace Taopix\Connector\Shopify\Entity;
use Taopix\Core\Entity\AbstractEntity;

/**
 * @see https://shopify.dev/api/admin/rest/reference/online-store/theme
 */
class Theme extends AbstractEntity
{
	/**
	 * @var int
	 */
	private $id = 0;

	/**
	 * @var string
	 */
	private $role = '';

	/**
	 * @var string
	 */
	private $name = '';

	/**
	 * Sets the ID property.
	 *
	 * @param int $pID ID to set.
	 * @return Theme Theme instance
	 */
	public function setId(int $pID): Theme
	{
		$this->id = $pID;
		return $this;
	}

	/**
	 * Returns the ID value.
	 *
	 * @return int ID value.
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * Sets the role property.
	 *
	 * @param string $pRole Role to set.
	 * @return Theme Theme instance.
	 */
	public function setRole(string $pRole): Theme
	{
		$this->role = $pRole;
		return $this;
	}

	/**
	 * Returns the role value.
	 *
	 * @return string Role value.
	 */
	public function getRole(): string
	{
		return $this->role;
	}

	/**
	 * Sets the name property.
	 *
	 * @param string $pName Name to set.
	 * @return Theme Theme instance.
	 */
	public function setName(string $pName): Theme
	{
		$this->name = $pName;
		return $this;
	}

	/**
	 * Returns the name value.
	 *
	 * @return string Name value.
	 */
	public function getName(): string
	{
		return $this->name;
	}
}
