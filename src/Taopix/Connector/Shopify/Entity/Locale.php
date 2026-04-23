<?php

namespace Taopix\Connector\Shopify\Entity;
use Taopix\Core\Entity\AbstractEntity;

/**
 * @see https://shopify.dev/api/admin/graphql/reference/translations/shoplocale
 */
class Locale extends AbstractEntity
{
	/**
	 * @var string
	 */
	private $locale = '';

	/**
	 * @var bool
	 */
	private $isPrimary = false;

	/**
	 * Sets the ID property.
	 *
	 * @param string $pLocale locale to set.
	 * @return Locale 
	 */
	public function setLocale(string $pLocale): Locale
	{
		$this->locale = $pLocale;
		return $this;
	}

	/**
	 * Returns the locale .
	 *
	 * @return string locale value.
	 */
	public function getLocale(): string
	{
		return $this->locale;
	}

	/**
	 * Sets the isPrimary property.
	 *
	 * @param bool $pLocale Role to set.
	 * @return Locale 
	 */
	public function setIsPrimary(bool $pIsPrimary): Locale
	{
		$this->isPrimary = $pIsPrimary;
		return $this;
	}

	/**
	 * Returns the isPrimary value.
	 *
	 * @return bool is primary.
	 */
	public function getIsPrimary(): bool
	{
		return $this->isPrimary;
	}
}
