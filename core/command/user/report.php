<?php

namespace OC\Core\Command\User;

use OCP\IUserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Report extends Command {
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
			->setName('user:report')
			->setDescription('shows how many users have access');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		/** @var \Symfony\Component\Console\Helper\TableHelper $table */
		$table = $this->getHelperSet()->get('table');
		$table->setHeaders(array('User Report', ''));
		$userCountArray = $this->countUsers();
		if(!empty($userCountArray)) {
			$total = 0;
			$rows = array();
			foreach($userCountArray as $classname => $users) {
				$total += $users;
				$rows[] = array($classname, $users);
			}

			$rows[] = array(' ');
			$rows[] = array('total users', $total);
		} else {
			$rows[] = array('No backend enabled that supports user counting', '');
		}

		$userDirectoryCount = $this->countUserDirectories();
		$rows[] = array(' ');
		$rows[] = array('user directories', $userDirectoryCount);

		$table->setRows($rows);
		$table->render($output);
	}

	private function countUsers() {
		return $this->userManager->countUsers();
	}

	private function countUserDirectories() {
		$dataview = new \OC\Files\View('/');
		$userDirectories = $dataview->getDirectoryContent('/', 'httpd/unix-directory');
		return count($userDirectories);
	}
}
