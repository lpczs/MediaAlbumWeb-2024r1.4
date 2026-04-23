<?php

namespace Taopix\Connector\Shopify\Entity;

use Taopix\Core\Entity\AbstractEntity;

/**
 * @see https://shopify.dev/docs/admin-api/rest/reference/metafield
 */
class MetaField extends AbstractEntity
{
	/**
	 * @var string
	 */
	private $namespace = '';

	/**
	 * @var string
	 */
	private $description = '';	

	/**
	 * @var string
	 */
	private $key = '';

	/**
	 * @var string
	 */
	private $id = '';

	/**
	 * @var string
	 */
	private $value = '';

	/**
	 * @var string
	 */
	private $type = 'single_line_text_field';

	/**
	 * Sets namespace property.
	 *
	 * @param string $pNamespace
	 * @return MetaField
	 */
	public function setNamespace(string $pNamespace): MetaField
	{
		$this->namespace = $pNamespace;
		return $this;
	}

	/**
	 * Returns the namespace value.
	 *
	 * @return string Metafield namespace.
	 */
	public function getNamespace(): string
	{
		return $this->namespace;
	}

	/**
	 * Sets description property.
	 *
	 * @param string $pDescription
	 * @return MetaField
	 */
	public function setDescription(string $pDescription): MetaField
	{
		$this->description = $pDescription;
		return $this;
	}

	/**
	 * Returns the description value.
	 *
	 * @return string Metafield description.
	 */
	public function getDescription(): string
	{
		return $this->description;
	}	

	/**
	 * Sets key property.
	 *
	 * @param string $pKey
	 * @return MetaField
	 */
	public function setKey(string $pKey): MetaField
	{
		$this->key = $pKey;
		return $this;
	}

	/**
	 * Returns the key value.
	 *
	 * @return string The metafield key.
	 */
	public function getKey(): string
	{
		return $this->key;
	}

	/**
	 * Sets id property.
	 *
	 * @param string $pId
	 * @return MetaField
	 */
	public function setId(string $pId): MetaField
	{
		$this->id = $pId;
		return $this;
	}

	/**
	 * Returns the id value.
	 *
	 * @return string The metafield id.
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * Sets value property.
	 *
	 * @param string $pValue
	 * @return MetaField
	 */
	public function setValue(string $pValue): MetaField
	{
		$this->value = $pValue;
		return $this;
	}

	/**
	 * Returns the value value.
	 *
	 * @return string The metafield value.
	 */
	public function getValue(): string
	{
		return $this->value;
	}

	/**
	 * Sets type property.
	 *
	 * @param string $pType
	 * @return MetaField
	 */
	public function setType(string $pType): MetaField
	{
		$this->type = $pType;
		return $this;
	}

	/**
	 * Returns the type value.
	 *
	 * @return string The metafield value type.
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * Returns object properties as an array.
	 *
	 * @return array Properties array.
	 */
	public function getProperties(): array
	{
		$properties = get_object_vars($this);
		if ($this->getId() === '') 
		{
			unset($properties['id']);
		}
		return $properties;
	}
}
