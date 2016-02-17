<?php


namespace OCA\Activity\Tests;

use OCA\Activity\AppInfo\Application;

class ApplicationTest extends TestCase {
	/** @var \OCA\Activity\AppInfo\Application */
	protected $app;

	/** @var \OCP\AppFramework\IAppContainer */
	protected $container;

	protected function setUp() {
		parent::setUp();
		$this->app = new Application();
		$this->container = $this->app->getContainer();
	}

	public function testContainerAppName() {
		$this->app = new Application();
		$this->assertEquals('activity', $this->container->getAppName());
	}

	public function queryData() {
		return array(
			array('ActivityData', 'OCA\Activity\Data'),
			array('ActivityL10N', 'OCP\IL10N'),
			array('Consumer', 'OCA\Activity\Consumer'),
			array('Consumer', 'OCP\Activity\IConsumer'),
			array('DataHelper', 'OCA\Activity\DataHelper'),
			array('DisplayHelper', 'OCA\Activity\Display'),
			array('GroupHelper', 'OCA\Activity\GroupHelper'),
			array('Hooks', 'OCA\Activity\FilesHooks'),
			array('MailQueueHandler', 'OCA\Activity\MailQueueHandler'),
			array('Navigation', 'OCA\Activity\Navigation'),
			array('UserSettings', 'OCA\Activity\UserSettings'),
			array('URLGenerator', 'OCP\IURLGenerator'),
			array('SettingsController', 'OCP\AppFramework\Controller'),
			array('ActivitiesController', 'OCP\AppFramework\Controller'),
			array('FeedController', 'OCP\AppFramework\Controller'),
		);
	}

	/**
	 * @dataProvider queryData
	 * @param string $service
	 * @param string $expected
	 */
	public function testContainerQuery($service, $expected) {
		$this->assertTrue($this->container->query($service) instanceof $expected);
	}
}
