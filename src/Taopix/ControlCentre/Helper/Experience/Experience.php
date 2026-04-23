<?php

namespace Taopix\ControlCentre\Helper\Experience;
use Doctrine\Persistence\ManagerRegistry;
use Taopix\ControlCentre\Enum\Product;

class Experience
{
    private array $keyMap = [];
    private array $baseExperienceArray = [];
    private array $calendarBaseExperienceArray = [];
    private array $retroPrintBaseExperienceArray = [];
    private array $schemaArray = [];

    public function __construct()
	{
        $this->baseExperienceArray = json_decode(file_get_contents(__DIR__ . '/baseExperience.json', true),true);
        $this->calendarBaseExperienceArray = json_decode(file_get_contents(__DIR__ . '/calendarBaseExperience.json', true),true);
        $this->retroPrintBaseExperienceArray = json_decode(file_get_contents(__DIR__ . '/retroPrintBaseExperience.json', true),true);
        $this->schemaArray = json_decode(file_get_contents(__DIR__ . '/experienceFormSchema.json', true),true);
        $this->keyMap = json_decode(file_get_contents(__DIR__ . '/keyMap.json', true),true);
	}

    /**
     * Return base experience data as array
     *
     * @return array
     */
    public function getBaseExperience(int $productType, bool $isRetroPrint): array
    {
        return ($productType === Product\Type::CALENDAR->value) ? $this->calendarBaseExperienceArray : ( ($isRetroPrint) ? $this->retroPrintBaseExperienceArray : $this->baseExperienceArray );
    }

    /**
     * Return schema data as array
     *
     * @return array
     */
    public function getSchemaArray(): array
    {
        return $this->schemaArray;
    }

    /**
     * Return base experience data as array
     *
     * @return array
     */
    public function getKeyMap(): array
    {
        return $this->keyMap;
    }

	/**
     * Rename Array Keys using the keyMap array
     *
     * @param array $array
     * @param bool $hydrate - are we hydrating or the reverse
     * @return array reMapped array
     */
    public function keyRename(array $array, bool $hydrate = true): array 
    {
        $returnArray = [];
        $keyMap = $this->getKeyMap();

        if (!$hydrate) {
            $keyMap = array_flip($keyMap);
        }

        foreach($array as $origKey=>$value) {
            if($newKey=array_search($origKey,$keyMap)) {
              if (is_array($value)) {
                  $value = self::keyRename($value, $hydrate);
              }
              $returnArray[$newKey]=$value;
              if ($newKey !== $origKey) {
                unset($array[$origKey]);
              }
            }
        }
        return $returnArray;     
    }
}