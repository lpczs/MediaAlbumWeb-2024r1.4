<?php

namespace Taopix\Core;

abstract class Debugable
{
	private $debugService;

	public function setDebugService(\Taopix\Core\Services\DebugService $pDebugService)
	{
		$this->debugService = $pDebugService;
	}

	public function getDebugService()
	{
		return $this->debugService;
	}

	public function debug($pMessage)
	{
		if ($this->debugService)
		{
			$this->debugService->debug($pMessage);
		}
	}

	public function info($pMessage)
	{
		if ($this->debugService)
		{
			$this->debugService->info($pMessage);
		}
	}	

	public function error($pMessage)
	{
		if ($this->debugService)
		{
			$this->debugService->error($pMessage);
		}
	}

}