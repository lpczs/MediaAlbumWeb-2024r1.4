<?php

class sessionUpdatePricingEngine extends ExternalScript
{
	public function run()
	{
		if ($this->mode == 'upgrade')
		{
			// mark all old orders as using the legacy pricing engine to avoid any issues caused by new pricing engine effecting old order prices
			$this->addSessionChange(array('path' => array('order'),
											'changes' => array(
												array('key' => 'uselegacypricingsystem', 'insert' => 1)
											)));
		}
	}
}

?>