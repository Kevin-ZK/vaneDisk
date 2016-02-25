<?php

namespace OCA\user_ldap\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \OCA\user_ldap\lib\Helper;

class DeleteConfig extends Command {
	/** @var \OCA\User_LDAP\lib\Helper */
	protected $helper;

	/**
	 * @param Helper $helper
	 */
	public function __construct(Helper $helper) {
		$this->helper = $helper;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setName('ldap:delete-config')
			->setDescription('deletes an existing LDAP configuration')
			->addArgument(
					'configID',
					InputArgument::REQUIRED,
					'the configuration ID'
				     )
		;
	}


	protected function execute(InputInterface $input, OutputInterface $output) {
		$configPrefix = $input->getArgument('configID');

		$success = $this->helper->deleteServerConfiguration($configPrefix);

		if($success) {
			$output->writeln("Deleted configuration with configID '{$configPrefix}'");
		} else {
			$output->writeln("Cannot delete configuration with configID '{$configPrefix}'");
		}
	}
}
