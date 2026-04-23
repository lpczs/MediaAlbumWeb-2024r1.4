<?php

namespace Taopix\ControlCentre\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Taopix\Core\Config\Config;
use Taopix\Kernel;

class ControlCentreConfig implements CompilerPassInterface
{
	private Kernel $kernel;

	public function __construct(Kernel $kernel)
	{
		$this->kernel = $kernel;
	}

	public function process(ContainerBuilder $container): void
	{
		$configReader = new Config($this->kernel->getProjectDir() . '/config/mediaalbumweb.conf');

		$config = $configReader->getGlobalConfig();

		$container->getParameterBag()->set('maw.config', $config);

		$container->getParameterBag()->set('maw.dbmap', [
			'controlcentre' => $config['DBNAME']
		]);
	}
}