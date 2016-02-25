<?php

namespace OC\Core\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Status extends Base {
	protected function configure() {
		parent::configure();

		$this
			->setName('status')
			->setDescription('show some status information')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$values = array(
			'installed' => (bool) \OC_Config::getValue('installed'),
			'version' => implode('.', \OC_Util::getVersion()),
			'versionstring' => \OC_Util::getVersionString(),
			'edition' => \OC_Util::getEditionString(),
		);

		$this->writeArrayInOutputFormat($input, $output, $values);
	}
}
