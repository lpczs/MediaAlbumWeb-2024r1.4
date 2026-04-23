<?php

namespace Taopix\Core\Outputter;

class OutputterTTY implements \Taopix\Core\Outputter\IOutputter
{
	public function output($pMessage = "", $pNoCarriageReturn = false)
	{
		if (is_array($pMessage))
		{
			print_r($pMessage);
		}
		else
		{
			echo $pMessage;
		}

		if (!$pNoCarriageReturn)
		{
			echo PHP_EOL;
		}
	}

	public function error($pMessage, $pReturnCode = 1)
	{
		$this->output(PHP_EOL . "***** ERROR *****");
		$this->output($pMessage);
		$this->output("*****************" . PHP_EOL);
		end_proc($pReturnCode);
	}

}

?>