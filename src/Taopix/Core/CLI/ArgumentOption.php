<?php

namespace Taopix\Core\CLI;

class ArgumentOption
{
	protected $short;
	protected $long;
	protected $description;

	function __construct($pShort, $pLong, $pDescription, $pHidden = false)
	{
		$this->short = $pShort;
		$this->long = $pLong;
		$this->description = $pDescription;
		$this->hidden = $pHidden;
	}

    public function __get($name)
    {
        return $this->$name;
    }

	function __toString()
	{
		if (!$this->hidden)
		{

			$spaces = "";

			$first = str_replace(":", "", $this->short) . " | " . str_replace(":", "", $this->long);

			$spaces = str_pad(" ", 20 - strlen($first));

			return $first . $spaces . " - " . $this->description;
		}
		else
		{
			return "";
		}
	}
}

?>