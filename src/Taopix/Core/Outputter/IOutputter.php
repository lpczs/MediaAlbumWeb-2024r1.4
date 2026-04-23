<?php

namespace Taopix\Core\Outputter;

interface IOutputter
{
	public function output($pMessage = "", $pNoCarriageReturn = false);
}

?>