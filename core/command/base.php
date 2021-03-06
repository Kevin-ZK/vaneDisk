<?php

namespace OC\Core\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Base extends Command {
	protected function configure() {
		$this
			->addOption(
				'output',
				null,
				InputOption::VALUE_OPTIONAL,
				'Output format (plain, json or json_pretty, default is plain)',
				'plain'
			)
		;
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @param array $items
	 */
	protected function writeArrayInOutputFormat(InputInterface $input, OutputInterface $output, $items) {
		switch ($input->getOption('output')) {
			case 'json':
				$output->writeln(json_encode($items));
				break;
			case 'json_pretty':
				$output->writeln(json_encode($items, JSON_PRETTY_PRINT));
				break;
			default:
				foreach ($items as $key => $item) {
					if (!is_int($key)) {
						$value = $this->valueToString($item);
						if (!is_null($value)) {
							$output->writeln(' - ' . $key . ': ' . $value);
						} else {
							$output->writeln(' - ' . $key);
						}
					} else {
						$output->writeln(' - ' . $this->valueToString($item));
					}
				}
				break;
		}
	}

	protected function valueToString($value) {
		if ($value === false) {
			return 'false';
		} else if ($value === true) {
			return 'true';
		} else if ($value === null) {
			null;
		} else {
			return $value;
		}
	}
}
