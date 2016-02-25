<?php

namespace OC\Core\Command\App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCode extends Command {
	protected function configure() {
		$this
			->setName('app:check-code')
			->setDescription('check code to be compliant')
			->addArgument(
				'app-id',
				InputArgument::REQUIRED,
				'check the specified app'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$appId = $input->getArgument('app-id');
		$codeChecker = new \OC\App\CodeChecker();
		$codeChecker->listen('CodeChecker', 'analyseFileBegin', function($params) use ($output) {
			if(OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
				$output->writeln("<info>Analysing {$params}</info>");
			}
		});
		$codeChecker->listen('CodeChecker', 'analyseFileFinished', function($filename, $errors) use ($output) {
			$count = count($errors);

			// show filename if the verbosity is low, but there are errors in a file
			if($count > 0 && OutputInterface::VERBOSITY_VERBOSE > $output->getVerbosity()) {
				$output->writeln("<info>Analysing {$filename}</info>");
			}

			// show error count if there are errros present or the verbosity is high
			if($count > 0 || OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
				$output->writeln(" {$count} errors");
			}
			usort($errors, function($a, $b) {
				return $a['line'] >$b['line'];
			});

			foreach($errors as $p) {
				$line = sprintf("%' 4d", $p['line']);
				$output->writeln("    <error>line $line: {$p['disallowedToken']} - {$p['reason']}</error>");
			}
		});
		$errors = $codeChecker->analyse($appId);
		if (empty($errors)) {
			$output->writeln('<info>App is compliant - awesome job!</info>');
		} else {
			$output->writeln('<error>App is not compliant</error>');
			return 1;
		}
	}
}
