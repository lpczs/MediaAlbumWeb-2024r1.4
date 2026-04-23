<?php

namespace Taopix\Connector\Shopify\Entity;

use Taopix\Core\Entity\AbstractEntity;

/**
 * @see https://shopify.dev/docs/admin-api/graphql/reference/common-objects/imageinput
 */
class ProductImage extends AbstractEntity
{
	/**
	 * @var string
	 */
	private $altText = null;

	/**
	 * @var string
	 */
	private $id = null;

	/**
	 * @var string
	 */
	private $src = null;

	/**
	 * Sets alt property.
	 *
	 * @param string $pAlt Alt text to set.
	 * @return ProductImage ProductImage instance.
	 */
	public function setAltText(string $pAlt): ProductImage
	{
		$this->altText = $pAlt;
		return $this;
	}

	/**
	 * Returns alt value.
	 *
	 * @return string Alt value.
	 */
	public function getAltText(): string
	{
		return $this->altText;
	}

	/**
	 * Sets ID property.
	 *
	 * @param string $pID ID to set.
	 * @return ProductImage ProductImage instance.
	 */
	public function setId(string $pID): ProductImage
	{
		$this->id = $pID;
		return $this;
	}

	/**
	 * Returns ID value.
	 *
	 * @return string ID value.
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * Sets src property.
	 *
	 * @param string $psrc src to set.
	 * @return ProductImage ProductImage instance.
	 */
	public function setSrc(string $pSrc): ProductImage
	{
		$this->src = $pSrc;
		return $this;
	}

	/**
	 * Returns src value.
	 *
	 * @return string Src value.
	 */
	public function getSrc(): string
	{
		return $this->src;
	}

	/**
	 * Returns object properties as an array.
	 *
	 * @return array Properties array.
	 */
	public function getProperties(): array
	{
		$productImageProperties = get_object_vars($this);

		if ($productImageProperties['id'] == '') {
			unset($productImageProperties['id']);
		}

		if ($productImageProperties['altText'] == '') {
			unset($productImageProperties['altText']);
		}

		if ($productImageProperties['src'] == '') {
			unset($productImageProperties['src']);
		}
		return $productImageProperties;
	}
}
