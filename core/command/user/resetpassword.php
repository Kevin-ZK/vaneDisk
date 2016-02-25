<?php

namespace OC\Core\Command\User;

use OCP\IUserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ResetPassword extends Command {

	/** @var IUserManager */
	protected $userManager;

	public function __construct(IUserManager $userManager) {
		$this->userManager = $userManager;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setName('user:resetpassword')
			->setDescription('Resets the password of the named user')
			->addArgument(
				'user',
				InputArgument::REQUIRED,
				'Username to reset password'
			)
			->addOption(
				'password-from-env',
				null,
				InputOption::VALUE_NONE,
				'read password from environment variable OC_PASS'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$username = $input->getArgument('user');

		/** @var $user \OCP\IUser */
		$user = $this->userManager->get($username);
		if (is_null($user)) {
			$output->writeln('<error>User does not exist</error>');
			return 1;
		}

		if ($input->getOption('password-from-env')) {
			$password = getenv('OC_PASS');
			if (!$password) {
				$output->writeln('<error>--password-from-env given, but OC_PASS is empty!</error>');
				return 1;
			}
		} elseif ($input->isInteractive()) {
			/** @var $dialog \Symfony\Component\Console\Helper\DialogHelper */
			$dialog = $this->getHelperSet()->get('dialog');

			if (\OCP\App::isEnabled('encryption')) {
				$output->writeln(
					'<error>Warning: Resetting the password when using encryption will result in data loss!</error>'
				);
				if (!$dialog->askConfirmation($output, '<question>Do you want to continue?</question>', true)) {
					return 1;
				}
			}

			$password = $dialog->askHiddenResponse(
				$output,
				'<question>Enter a new password: </question>',
				false
			);
			$confirm = $dialog->askHiddenResponse(
				$output,
				'<question>Confirm the new password: </question>',
				false
			);

			if ($password !== $confirm) {
				$output->writeln("<error>Passwords did not match!</error>");
				return 1;
			}
		} else {
			$output->writeln("<error>Interactive input or --password-from-env is needed for entering a new password!</error>");
			return 1;
		}

		$success = $user->setPassword($password);
		if ($success) {
			$output->writeln("<info>Successfully reset password for " . $username . "</info>");
		} else {
			$output->writeln("<error>Error while resetting password!</error>");
			return 1;
		}
	}
}
