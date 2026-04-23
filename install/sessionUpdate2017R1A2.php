<?php

class sessionUpdate2017R1A2 extends ExternalScript
{
	public function run()
	{
		if ($this->mode == 'upgrade')
		{
			// add new key to prevent hijacking of the shopping cart
			$this->addSessionChange(array('path' => array(),
											'changes' => array(
												array('key' => 'authenticatecookie', 'insert' => 1)
											)));
		}
	}
}

?>