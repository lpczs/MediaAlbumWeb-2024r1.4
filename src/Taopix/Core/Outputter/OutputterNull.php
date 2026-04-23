<?php

namespace Taopix\Core\Outputter;

class OutputterNull implements \Taopix\Core\Outputter\IOutputter
{
	public function output($pMessage = "", $pNoCarriageReturn = false)
	{
		return null;
	}

	public function error($pMessage, $pReturnCode = 1)
	{
		end_proc($pReturnCode);
	}

}

?>