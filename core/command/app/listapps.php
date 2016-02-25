<?php

namespace OC\Core\Command\App;

use OC\Core\Command\Base;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListApps extends Base {
	protected function configure() {
		parent::configure();

		$this
			->setName('app:list')
			->setDescription('List all available apps')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$apps = \OC_App::getAllApps();
		$enabledApps = $disabledApps = [];
		$versions = \OC_App::getAppVersions();

		//sort enabled apps above disabled apps
		foreach ($apps as $app) {
			if (\OC_App::isEnabled($app)) {
				$enabledApps[] = $app;
			} else {
				$disabledApps[] = $app;
			}
		}

		$apps = ['enabled' => [], 'disabled' => []];

		sort($enabledApps);
		foreach ($enabledApps as $app) {
			$apps['enabled'][$app] = (isset($versions[$app])) ? $versions[$app] : true;
		}

		sort($disabledApps);
		foreach ($disabledApps as $app) {
			$apps['disabled'][$app] = null;
		}

		$this->writeAppList($input, $output, $apps);
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @param array $items
	 */
	protected function writeAppList(InputInterface $input, OutputInterface $output, $items) {
		switch ($input->getOption('output')) {
			case 'plain':
				$output->writeln('Enabled:');
				parent::writeArrayInOutputFormat($input, $output, $items['enabled']);

				$output->writeln('Disabled:');
				parent::writeArrayInOutputFormat($input, $output, $items['disabled']);
			break;

			default:
				parent::writeArrayInOutputFormat($input, $output, $items);
			break;
		}
	}
}
