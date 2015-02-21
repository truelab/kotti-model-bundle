<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel {
	public function registerBundles() {
		return array(
			new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),

			new Truelab\KottiModelBundle\TruelabKottiModelBundle(),
		);
	}

	public function registerContainerConfiguration(LoaderInterface $loader) {
		$loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
	}

	/**
	 * @return string
	 */
	public function getCacheDir() {
		return sys_get_temp_dir() . '/TruelabKottiModelBundle/cache';
	}

	/**
	 * @return string
	 */
	public function getLogDir() {
		return sys_get_temp_dir() . '/TruelabKottiModelBundle/logs';
	}

}
