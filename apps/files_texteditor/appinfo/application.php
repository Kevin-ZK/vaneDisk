<?php

namespace OCA\Files_Texteditor\AppInfo;

use OC\Files\View;
use OCA\Files_Texteditor\Controller\FileHandlingController;
use OCP\AppFramework\App;
use OCP\AppFramework\IAppContainer;
use Punic\Exception;

class Application extends App {

	/**
	 * @param array $urlParams
	 */
	public function __construct(array $urlParams = array()) {
		parent::__construct('files_texteditor', $urlParams);

		$container = $this->getContainer();
		$server = $container->getServer();

		$container->registerService('FileHandlingController', function (IAppContainer $c) use ($server) {
			$user = $server->getUserSession()->getUser();
			if ($user) {
				$uid = $user->getUID();
			} else {
				throw new \BadMethodCallException('no user logged in');
			}
			return new FileHandlingController(
				$c->getAppName(),
				$server->getRequest(),
				$server->getL10N($c->getAppName()),
				new View('/' . $uid . '/files'),
				$server->getLogger()
			);
		});
	}
}
