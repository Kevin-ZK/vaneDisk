<?php

namespace OC\Core\Command\Encryption;

use OCP\IConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Disable extends Command {
	/** @var IConfig */
	protected $config;

	/**
	 * @param IConfig $config
	 */
	public function __construct(IConfig $config) {
		parent::__construct();
		$this->config = $config;
	}

	protected function configure() {
		$this
			->setName('encryption:disable')
			->setDescription('Disable encryption')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		if ($this->config->getAppValue('core', 'encryption_enabled', 'no') !== 'yes') {
			$output->writeln('Encryption is already disabled');
		} else {
			$this->config->setAppValue('core', 'encryption_enabled', 'no');
			$output->writeln('<info>Encryption disabled</info>');
		}
	}
}
