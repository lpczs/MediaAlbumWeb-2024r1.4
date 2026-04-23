<?php

namespace Taopix\Core;

class Arguments extends HashMap
{
	private $parameters;

	function __construct($pParameters)
	{
		$this->parameters = $pParameters;

		$shortOptions = array_map(function($pParameter)
		{
			return $pParameter->short;
		}, $pParameters);

		$longOptions = array_map(function($pParameter)
		{
			return $pParameter->long;
		}, $pParameters);

		$this->set(new HashMap(getopt(implode('', $shortOptions), $longOptions)));
	}

	public function usage()
	{	
		return implode("\n", array_filter(
								array_map(function($pParameter)
								{
									$description = (string)$pParameter;

									if ($description != "")
									{
										return (string)$pParameter;
									}
								}, $this->parameters), 
								function($pParameter)
								{
									return $pParameter != "";
								}));
	}

	private function getParamValues($pOffset)
	{
		$return = null;

		foreach ($this->parameters as $parameter)
		{

			$long = str_replace(":", "", $parameter->long);
			$short = str_replace(":", "", $parameter->short);

			if ($short == $pOffset)
			{
				$return = $parameter;
			}
			else if ($long == $pOffset)
			{
				$return = $parameter;	
			}
		}

		return $return;
	}

    public function offsetGet($pOffset) 
    {
		$params = $this->getParamValues($pOffset);

		$long = str_replace(":", "", $params->long);
		$short = str_replace(":", "", $params->short);

		if ($params !== null)
		{
			$return = isset($this->container[$long]) ? $this->container[$long] : null;;

			if ($return === null)
			{
				$return = isset($this->container[$short]) ? $this->container[$short] : null;				
			}

			if ($return === false)
			{
				$return = true;
			}
			else if ($return === null)
			{
				$return = false;
			}
		}
		else
		{
    		$return = isset($this->container[$pOffset]) ? $this->container[$pOffset] : null;
    	}

        return $return;
    }
}

?>