<?php

namespace Taopix;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Taopix\ControlCentre\DependencyInjection\ControlCentreConfig;
use Symfony\Component\HttpKernel\RebootableInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

class Kernel extends BaseKernel implements KernelInterface, RebootableInterface, TerminableInterface
{
    use MicroKernelTrait;

	private function getConfigDir(): string
	{
		return $this->getProjectDir() . '/config/flex';
	}

	public function getProjectDir(): string
	{
		return dirname(__DIR__, 2);
	}

	public function getCacheDir(): string
	{
		return $this->getProjectDir() . '/../var/controlcentre/cache/'. $this->environment;
	}

	public function getLogDir(): string
	{
		return $this->getProjectDir() . '/../var/controlcentre/log/'. $this->environment;
	}

	protected function build(ContainerBuilder $container): void
	{
		$container->addCompilerPass(new ControlCentreConfig($this));
	}
}
