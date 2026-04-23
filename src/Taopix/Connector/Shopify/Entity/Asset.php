<?php

namespace Taopix\Connector\Shopify\Entity;
use Taopix\Core\Entity\AbstractEntity;

/**
 * @see https://shopify.dev/docs/admin-api/rest/reference/online-store/asset
 */
class Asset extends AbstractEntity
{
	/**
	 * @var string
	 */
	private $key = '';

	/**
	 * @var string
	 */
	private $publicUrl = '';

	/**
	 * @var string
	 */
	private $value = '';

	/**
	 * Sets the key property.
	 *
	 * @param string $pKey Key value to set.
	 * @return Asset Asset instance.
	 */
	public function setKey(string $pKey): Asset
	{
		$this->key = $pKey;
		return $this;
	}

	/**
	 * Returns the key value.
	 *
	 * @return string Key value.
	 */
	public function getKey(): string
	{
		return $this->key;
	}

	/**
	 * Sets the public URL property.
	 *
	 * @param string $pPublicURL Public URL to set.
	 * @return Asset Asset instance.
	 */
	public function setPublicUrl(string $pPublicURL): Asset
	{
		$this->publicUrl = $pPublicURL;
		return $this;
	}

	/**
	 * Returns the public URL value.
	 *
	 * @return string Public URL value.
	 */
	public function getPublicUrl(): string
	{
		return $this->publicUrl;
	}

	/**
	 * Sets the value property.
	 *
	 * @param string $pValue
	 * @return Asset Asset instance.
	 */
	public function setValue(string $pValue): Asset
	{
		$this->value = $pValue;
		return $this;
	}

	/**
	 * Returns the value value.
	 *
	 * @return string Value value.
	 */
	public function getValue(): string
	{
		return $this->value;
	}
}
