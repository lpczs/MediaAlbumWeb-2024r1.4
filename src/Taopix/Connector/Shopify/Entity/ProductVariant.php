<?php

namespace Taopix\Connector\Shopify\Entity;

use Taopix\Core\Entity\AbstractEntity;
use Taopix\Connector\Shopify\Collection\MetaFieldCollection;

/**
 * @see https://shopify.dev/docs/admin-api/graphql/reference/products-and-collections/productvariantinput
 */
class ProductVariant extends AbstractEntity
{
	/**
	 * @var string
	 */
	private $id = '';

	/**
	 * @var string
	 */
	private $title = '';

	/**
	 * @var string
	 */
	private $imageSrc = '';	

	/**
	 * @var float
	 */
	private $price = 0.00;

	/**
	 * @var array
	 */
	private $inventoryItem;

	/**
	 * @var float
	 */
	private $compareAtPrice = 0.00;

	/**
	 * @var float
	 */
	private $weight = 0.0;

	/**
	 * @var MetaFieldCollection
	 */
	private $metafields;

	/**
	 * @var array
	 */
	private $options;

	/**
	 * @var string
	 */
	private $sku = '';

	/**
	 * Sets the options property.
	 *
	 * @param array $pOptions Options to set.
	 * @return Product Product instance.
	 */
	public function setOptions(array $pOptions): ProductVariant
	{
		$this->options = $pOptions;
		return $this;
	}

	/**
	 * Returns the options value.
	 *
	 * @return array Options array.
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * Sets the ID property.
	 *
	 * @param string $pID ID to set.
	 * @return ProductVariant ProductVariant instance.
	 */
	public function setId(string $pID): ProductVariant
	{
		$this->id = $pID;
		return $this;
	}

	/**
	 * Returns the ID value.
	 *
	 * @return string ID value.
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * Sets the title property.
	 *
	 * @param string $pTitle Title to set.
	 * @return Product Product instance.
	 */
	public function setTitle(string $pTitle): ProductVariant
	{
		$this->title = $pTitle;
		return $this;
	}
	
	/**
	 * Returns the title value.
	 *
	 * @return string Title value.
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * Sets the imagesrc property.
	 *
	 * @param string $pImageSrc Title to set.
	 * @return Product Product instance.
	 */
	public function setImageSrc(string $pImageSrc): ProductVariant
	{
		$this->imageSrc = $pImageSrc;
		return $this;
	}
	
	/**
	 * Returns the image url.
	 *
	 * @return string
	 */
	public function getImageSrc(): string
	{
		return $this->imageSrc;
	}

	/**
	 * Sets the compare at price property.
	 *
	 * @param float $pCompareAtPrice The compareAtPrice to set. 
	 * @return ProductVariant ProductVariant instance.
	 */
	public function setCompareAtPrice(float $pCompareAtPrice): ProductVariant
	{
		$this->compareAtPrice = $pCompareAtPrice;
		return $this;
	}

	/**
	 * Returns the product variant compare at price property.
	 *
	 * @return float Compare at price value.
	 */
	public function getCompareAtPrice(): float
	{
		return $this->compareAtPrice;
	}

	/**
	 * Sets the price property.
	 *
	 * @param float $pPrice The price to set.
	 * @return ProductVariant ProductVariant instance.
	 */
	public function setPrice(float $pPrice): ProductVariant
	{
		$this->price = $pPrice;
		return $this;
	}

	/**
	 * Returns the product variant price property.
	 *
	 * @return float Price value.
	 */
	public function getPrice(): float
	{
		return $this->price;
	}

	/**
	 * Sets the inventoryItem property.
	 *
	 * @param array $pInventoryItem The cost to set.
	 * @return ProductVariant ProductVariant instance.
	 */
	public function setInventoryItem(array $pInventoryItem): ProductVariant
	{
		$this->inventoryItem = $pInventoryItem;
		return $this;
	}

	/**
	 * Returns the product variant inventoryItem property.
	 *
	 * @return array inventoruyItem value.
	 */
	public function getInventoryItem(): array
	{
		return $this->inventoryItem;
	}

	/**
	 * Sets weight property.
	 *
	 * @param float $pWeight The weight to set.
	 * @return ProductVariant ProductVariant instance.
	 */
	public function setWeight(float $pWeight): ProductVariant
	{
		$this->weight = $pWeight;
		return $this;
	}

	/**
	 * Returns the product variant weight property.
	 *
	 * @return float Product variant weight.
	 */
	public function getWeight(): float
	{
		return $this->weight;
	}

	/**
	 * Sets Metafield property.
	 *
	 * @param array $pMetaFields Array of metafields to set.
	 * @return ProductVariant ProductVarient instance.
	 */
	public function setMetaFields(array $pMetaFields): ProductVariant
	{
		$this->metafields = new MetaFieldCollection($pMetaFields);
		return $this;
	}

	/**
	 * Returns the metafield collection.
	 *
	 * @return MetaFieldCollection MetaFieldCollection instance.
	 */
	public function getMetaFields(): MetaFieldCollection
	{
		return $this->metafields;
	}

	/**
	 * Sets the SKU property.
	 *
	 * @param string $pSKU sku to set.
	 * @return Product Product instance.
	 */
	public function setSku(string $pSKU): ProductVariant
	{
		$this->sku = $pSKU;
		return $this;
	}
	
	/**
	 * Returns the sku value.
	 *
	 * @return string sku value.
	 */
	public function getSku(): string
	{
		return $this->sku;
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
				if ($data === '' || is_null($data))
				{
					unset($properties[$key]);
				}
			}
		}

		/* If title value is blank the retain name option has been chosen and we do not want to overwrite the variant title */
		if ($this->getTitle() === '') 
		{
			unset($properties['title']);
		}

		return $properties;
	}
}
