<?php

namespace Taopix\Connector\Shopify\Entity;
use Taopix\Connector\Shopify\Collection\MetaFieldCollection;
use Taopix\Connector\Shopify\Entity\Product;
use Taopix\Core\Entity\AbstractEntity;

/**
 * @see https://shopify.dev/api/admin/graphql/reference/products-and-collections/collectioncreate
 */
class ProductCollection extends AbstractEntity
{
	/**
	 * @var string
	 */
	private $id = '';

	/**
	 * @var string
	 */
	private $storeFrontID = '';

	/**
	 * @var string
	 */
	private $title = '';

	/**
	 * @var MetaFieldCollection
	*/
	private $metafields;

	/**
	 * Sets id property.
	 *
	 * @param string $pId Collection ID to set.
	 * @return ProductCollection
	 */
	public function setId(string $pId): ProductCollection
	{
		$this->id = $pId;
		return $this;
	}

	/**
	 * Returns the id value.
	 *
	 * @return string The collection id.
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * Sets storeFrontID property.
	 *
	 * @param string $pStoreFrontID Store front ID  to set.
	 * @return ProductCollection
	 */
	public function setStoreFrontID(string $pStoreFrontID): ProductCollection
	{
		$this->storeFrontID = $pStoreFrontID;
		return $this;
	}

	/**
	 * Returns the store front ID value.
	 *
	 * @return string The store front ID.
	 */
	public function getStoreFrontID(): string
	{
		return $this->storeFrontID;
	}

	/**
	 * Sets title property.
	 *
	 * @param string $pTitle Collection title to set.
	 * @return ProductCollection
	 */
	public function setTitle(string $pTitle): ProductCollection
	{
		$this->title = $pTitle;
		return $this;
	}

	/**
	 * Returns the title value.
	 *
	 * @return string The collection title.
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * Sets Metafield property.
	 *
	 * @param array $pMetaFields
	 * @return Product
	 */
	public function setMetaFields(array $pMetaFields): ProductCollection
	{
		$this->metafields = new MetaFieldCollection($pMetaFields);
		return $this;
	}

	/**
	 * Returns metafields.
	 *
	 * @return MetaFieldCollection 
	 */
	public function getMetaFields(): MetaFieldCollection
	{
		return $this->metafields;
	}

	/**
	 * Returns object properties as an array.
	 *
	 * @return array Properties array.
	 */
	public function getProperties(): array
	{
		$properties = get_object_vars($this);
		$properties['metafields'] = $this->getMetaFields()->getProperties();

		if ($this->getId() === '') 
		{
			unset($properties['id']);
		}

		if ($this->getStoreFrontID() === '') 
		{
			unset($properties['storeFrontID']);
		}

		foreach($properties as $key=>$data) 
		{
			if (is_countable($data)) {
				if (count($data) == 0)
				{
					unset($properties[$key]);
				}
			}
			else
			{
				if ($data === '')
				{
					unset($properties[$key]);
				}
			}
		}

		return $properties;
	}
}
