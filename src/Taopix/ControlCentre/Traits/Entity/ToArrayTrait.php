<?php

namespace Taopix\ControlCentre\Traits\Entity;

trait ToArrayTrait
{
	public function asArray(): array
	{
		$parentProps = [];

		if (false !== get_parent_class()) {
			$parentProps = parent::asArray();
		}

		$props = get_class_vars(self::class);
		$returnDetails = [];
		foreach ($props as $key => $default) {
			$returnDetails[$key] = $this->{$key};
		}

		return array_merge($parentProps, $returnDetails);
	}
}