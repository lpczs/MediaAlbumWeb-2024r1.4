<?php

namespace Taopix\Connector\Shopify\Collection;
use Taopix\Connector\Shopify\Entity\Theme as ThemeEntity;

class ThemeCollection extends \Taopix\Core\Collection\Collection
{
	function __construct($array = array(), $pType = "\Taopix\Connector\Shopify\Entity\Theme")
	{
		parent::__construct($array, $pType);
	}

	/**
	 * Returns a list of theme for the provided role.
	 *
	 * @param string $pRole Role to filter the themes by.
	 * @return ThemeEntity Filtered theme collection.
	 */
	public function getThemeByRole($pRole): ThemeEntity
	{
		return array_values(array_filter($this->getArrayCopy(), function($pTheme) use ($pRole)
		{
			return $pTheme->getRole() === $pRole;
		}))[0];
	}

	/**
	 * Returns an Array of themes
	 *
	 * @param string $pName Name to filter the themes by.
	 * @return Array of Themes found
	 */
	public function getThemeByName($pName): Array
	{
		return array_values(array_filter($this->getArrayCopy(), function($pTheme) use ($pName)
		{
			return $pTheme->getName() === $pName;
		}));
	}
}
