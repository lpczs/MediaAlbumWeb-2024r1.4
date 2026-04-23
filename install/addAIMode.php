<?php

class addAIMode extends ExternalScript
{
	public function run()
	{
		if ($this->mode === 'upgrade')
		{
			// Add new key to the session.
			$this->addSessionChange(array(
				'path' => array('items'),
				'changes' => array(array('key' => 'itemaimode', 'insert' => 0))
			));

			// Add new key to the external cart.
            $this->addExternalCartChange(array(
				'path' => array('items'),
				'changes' => array(array('key' => 'projectaimode', 'insert' => 0))
            ));
            
            // Add new key to the online basket.
            $this->addOnlineBasketChange(array(
				'path' => array('items'),
				'changes' => array(array('key' => 'projectaimode', 'insert' => 0))
			));
		}
	}
}

?>
