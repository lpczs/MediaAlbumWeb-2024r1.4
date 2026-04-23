<?php

namespace Taopix\Connector\Shopify\Entity;

use Taopix\Core\Entity\AbstractEntity;
use Taopix\Connector\Shopify\Collection\ProductImageCollection;
use Taopix\Connector\Shopify\Collection\ProductVariantCollection;
use Taopix\Connector\Shopify\Collection\MetaFieldCollection;

/**
 * @see https://shopify.dev/docs/admin-api/graphql/reference/products-and-collections/product
 */
class Product extends AbstractEntity
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
	private $descriptionHtml = '';

	/**
	 * @var string
	 */
	private $vendor = '';

	/**
	 * @var string
	 */
	private $productType = '';

	/**
	 * @var string
	 */
	private $handle = '';

	/**
	 * @var string
	 */
	private $tags = '';

	/**
	 * @var string
	 */
	private $status = 'ACTIVE';	

	/**
	 * @var bool
	 */
	private $published;

	/**
	 * @var MetaFieldCollection
	 */
	private $metafields;

	/**
	 * @var ProductImageCollection
	 */
	private $images;

	/**
	 * @var ProductVariantCollection
	 */
	private $variants;

	/**
	 * @var string
	 */
	private $storefrontId;

	/**
	 * @var array
	 */
	private $options;

	/**
	 * Sets the options property.
	 *
	 * @param array $pOptions Options to set.
	 * @return Product Product instance.
	 */
	public function setOptions(array $pOptions): Product
	{
		$this->options = $pOptions;
		return $this;
	}

	/**
	 * Returns the options value.
	 *
	 * @return array
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * Sets the title property.
	 *
	 * @param string $pTitle Title to set.
	 * @return Product Product instance.
	 */
	public function setTitle(string $pTitle): Product
	{
		$this->title = $pTitle;
		return $this;
	}

	/**
	 * Returns the title value.
	 *
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * Sets the description HTML property.
	 *
	 * @param string $pDescriptionHTML Description HTML to set.
	 * @return Product Product instance.
	 */
	public function setDescriptionHtml(string $pDescriptionHTML): Product
	{
		$this->descriptionHtml = $pDescriptionHTML;
		return $this;
	}

	/**
	 * Returns the description HTML value.
	 *
	 * @return string Description HTML value.
	 */
	public function getDescriptionHtml(): string
	{
		return $this->descriptionHtml;
	}

	/**
	 * Sets the vendor property.
	 *
	 * @param string $pVendor Vendor to set.
	 * @return Product Product instance.
	 */
	public function setVendor(string $pVendor): Product
	{
		$this->vendor = $pVendor;
		return $this;
	}

	/**
	 * Returns the vendor value.
	 *
	 * @return string Vendor value.
	 */
	public function getVendor(): string
	{
		return $this->vendor;
	}

	/**
	 * Sets the product type value.
	 *
	 * @param string $pProductType Product type to set.
	 * @return Product Product instance.
	 */
	public function setProductType(string $pProductType): Product
	{
		$this->productType = $pProductType;
		return $this;
	}

	/**
	 * Returns the product type value.
	 *
	 * @return string Product type value.
	 */
	public function getProductType(): string
	{
		return $this->productType;
	}

	/**
	 * Sets the handle property.
	 *
	 * @param string $pHandle Handle to set.
	 * @return Product Product instance
	 */
	public function setHandle(string $pHandle): Product
	{
		$this->handle = $pHandle;
		return $this;
	}

	/**
	 * Returns the handle value.
	 *
	 * @return string Handle value.
	 */
	public function getHandle(): string
	{
		return $this->handle;
	}

	/**
	 * Sets the tags property.
	 *
	 * @param string $pTags Comma seperated list of tags to set.
	 * @return Product product instance
	 */
	public function setTags(string $pTags): Product
	{
		$this->tags = $pTags;
		return $this;
	}

	/**
	 * Returns the tags value.
	 *
	 * @return string tags value.
	 */
	public function getTags(): string
	{
		return $this->tags;
	}

	/**
	 * Sets the status property.
	 *
	 * @param string $pStatus ACTIVE or DRAFT
	 * @return Product Product instance.
	 */
	public function setStatus(string $pStatus): Product
	{
		$this->status = $pStatus;
		return $this;
	}

	/**
	 * Returns the status value.
	 *
	 * @return string ACTIVE or DRAFT
	 */
	public function getStatus(): string
	{
		return $this->status;
	}	

	/**
	 * Sets the published property.
	 *
	 * @param bool $pPublished True to set the product as published.
	 * @return Product Product instance.
	 */
	public function setPublished(bool $pPublished): Product
	{
		$this->published = $pPublished;
		return $this;
	}

	/**
	 * Returns the publish value.
	 *
	 * @return bool True if the product is published.
	 */
	public function getPublished(): bool
	{
		return $this->published;
	}

	/**
	 * Sets the images property.
	 *
	 * @param array $pImageProperties Array of image properties to set.
	 * @return Product Product instance.
	 */
	public function setImages(array $pImageProperties): Product
	{
		$this->images = new ProductImageCollection($pImageProperties);
		return $this;
	}

	/**
	 * Returns images property.
	 *
	 * @return ProductImageCollection ProductImageCollection instance.
	 */
	public function getImages(): ProductImageCollection
	{
		return $this->images;
	}

	/**
	 * Sets variants property.
	 *
	 * @param array $pVariants
	 * @return Product
	 */
	public function setVariants(array $pVariants): Product
	{
		$this->variants = new ProductVariantCollection($pVariants);
		return $this;
	}

	/**
	 * Returns variants.
	 *
	 * @return ProductVariantsCollection 
	 */
	public function getVariants(): ProductVariantCollection
	{
		return $this->variants;
	}

	/**
	 * Sets variants property.
	 *
	 * @param array $pVariants
	 * @return Product
	 */
	public function setStorefrontId(array $pStoreFrontID): Product
	{
		$this->storefrontId = $pStoreFrontID;
		return $this;
	}

	/**
	 * Returns variants.
	 *
	 * @return ProductVariantsCollection 
	 */
	public function getStorefrontId(): string
	{
		return $this->storefrontId;
	}

	/**
	 * Sets id property.
	 *
	 * @param string $pId
	 * @return Product
	 */
	public function setId(string $pId): Product
	{
		$this->id = $pId;
		return $this;
	}

	/**
	 * Returns the id value.
	 *
	 * @return string The Product id.
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * Sets Metafield property.
	 *
	 * @param array $pMetaFields
	 * @return Product
	 */
	public function setMetaFields(array $pMetaFields): Product
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
		$properties['variants'] = $this->getVariants()->getProperties();
		$properties['images'] = $this->getImages()->getProperties();
		
		unset($properties['storefrontId']);

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
				if ($data === '')
				{
					unset($properties[$key]);
				}
			}
		}

		return $properties;
	}
}
