<?php

namespace OC\Core\Command\User;

use OCP\IUserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class LastSeen extends Command {
	/** @var IUserManager */
	protected $userManager;

	/**
	 * @param IUserManager $userManager
	 */
	public function __construct(IUserManager $userManager) {
		$this->userManager = $userManager;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setName('user:lastseen')
			->setDescription('shows when the user was logged it last time')
			->addArgument(
				'uid',
				InputArgument::REQUIRED,
				'the username'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$user = $this->userManager->get($input->getArgument('uid'));
		if(is_null($user)) {
			$output->writeln('<error>User does not exist</error>');
			return;
		}

		$lastLogin = $user->getLastLogin();
		if($lastLogin === 0) {
			$output->writeln('User ' . $user->getUID() .
				' has never logged in, yet.');
		} else {
			$date = new \DateTime();
			$date->setTimestamp($lastLogin);
			$output->writeln($user->getUID() .
				'`s last login: ' . $date->format('d.m.Y H:i'));
		}
	}
}
