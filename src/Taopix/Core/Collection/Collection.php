<?php
namespace Taopix\Core\Collection;
use Taopix\Core\Entity\AbstractEntity;

/**
 * Class Collection.
 * Keeps a collection of Entity objects.
 */
class Collection extends \ArrayObject
{
	/**
	 * The class type which the collection is for.
	 *
	 * @var string
	 */
    private $type = '';

	/**
	 * Track the number of items in the collection.
	 * 
	 * @var int
	 */
	private $count = -1;
	
	/**
	 * @param array $pArray Array of objects to add to the collection on initialisation.
	 * @param string $pType The type of objects the collection will contain.
	 */
	function __construct($pArray = array(), $pType = "")
	{ 
		$this->type = $pType;

		// Convert each item into an entity. The make function will throw an expection
		// if it is not valid. It will also convert any arrays into an asset object.
		$array = array_map(function($pItem)
		{
			return (is_array($pItem)) ? $this->make($pItem) : $pItem;
        }, $pArray);

		parent::__construct($array, \ArrayObject::ARRAY_AS_PROPS);
	}

	/**
	 * Convert the item back to an array.
	 *
	 * @return array Array representation of the object.
	 */
	public function toArray(): array
	{
		return array_map(function ($pInstance)
		{
			return $pInstance->toArray();
		}, $this->getArrayCopy());
	}

	/**
	 * Convert the item back to an array.
	 *
	 * @param function $pConversionFunction Function to perform extra data conversions etc.
	 * @return array Array representation of the object.
	 */
	public function executeToArray($pConversionFunction)
	{
		return array_map(function ($pInstance) use ($pConversionFunction)
		{
			return $pInstance->executeToArray($pConversionFunction);
		}, $this->getArrayCopy());
	}

	/** 
	 * Returns The number of entry into the collection.
	 *
	 * @return int Number of entry into the collection.
	 */
	public function count(): int
	{
		// Check if the count need to be calculated.
		if ($this->count === -1)
		{
			$this->count = parent::count();
		}

		return $this->count;
	}

	/**
	 * Convert the item in pEntity into an object of the type in the type variable if it isn't already of that type.
	 * Throws an InvalidObjectType exception if the data in pEntity is incorrect.
	 *
	 * @param array|AbstractEntity $pEntity
	 * @return AbstractEntity
	 */
	public function make($pEntity): AbstractEntity
	{
		$entity = $pEntity;

		if (! $entity instanceof $this->type)
		{
			if (is_array($entity))
			{
				$entity = $this->type::make($entity);
			}
			else
			{
				throw new \Exception("Invalid type for Collection[" . $this->type . "]: " . var_export($entity, true));
			}
		}

		return $entity;
	}

	public function getProperties(): array
	{
		$propertyList = [];

		foreach($this as $instance)
		{
			$propertyList[] = $instance->getProperties();
		}

		return $propertyList;
	}
}
