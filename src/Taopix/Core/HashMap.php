<?php

namespace Taopix\Core;

class HashMap implements \ArrayAccess
{
    protected $container = array(); 

    public function __construct($pContainer = array())
    {
    	if (count($pContainer) > 0)
    	{
    		$this->container = $pContainer;
    	}
    }

    protected function set(HashMap $pHash)
    {
        $this->container = $pHash->asArray();
    }

    public function offsetSet($offset, $value) 
    {
        if (is_null($offset)) 
        {
            $this->container[] = $value;
        }
        else 
        {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) 
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) 
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) 
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function asArray()
    {
    	return $this->container;
    }

    public function __toString()
    {
        $returnString = "";

        if (count($this->container) > 0)
        {
            foreach ($this->container as $key => $value)
            {
                $returnString.= $key . "=" . $value . "\r";
            }
        }

        return $returnString;
    }

}

?>