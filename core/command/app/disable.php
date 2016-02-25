<?php

namespace OC\Core\Command\App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Disable extends Command {
	protected function configure() {
		$this
			->setName('app:disable')
			->setDescription('disable an app')
			->addArgument(
				'app-id',
				InputArgument::REQUIRED,
				'disable the specified app'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$appId = $input->getArgument('app-id');
		if (\OC_App::isEnabled($appId)) {
			try {
				\OC_App::disable($appId);
				$output->writeln($appId . ' disabled');
			} catch(\Exception $e) {
				$output->writeln($e->getMessage());
			}
		} else {
			$output->writeln('No such app enabled: ' . $appId);
		}
	}
}
