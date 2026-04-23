<?php

namespace Taopix\Connector\Shopify\Collection;
use Taopix\Connector\Shopify\Entity\Locale as LocaleEntity;

class LocaleCollection extends \Taopix\Core\Collection\Collection
{
	function __construct($array = array(), $pType = "\Taopix\Connector\Shopify\Entity\Locale")
	{
		parent::__construct($array, $pType);
	}

	/**
	 * Returns primary locale for the store.
	 *
	 * @return LocaleEntity Filtered locale collection.
	 */
	public function getPrimaryLocale(): LocaleEntity
	{
		return array_values(array_filter($this->getArrayCopy(), function($pLocale)
		{
			return $pLocale->getIsPrimary() === true;
		}))[0];
	}
}
