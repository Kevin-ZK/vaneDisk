<?php

namespace OCA\Encryption\Tests;


use OCA\Encryption\HookManager;
use Test\TestCase;

class HookManagerTest extends TestCase {

	/**
	 * @var HookManager
	 */
	private static $instance;

	/**
	 *
	 */
	public function testRegisterHookWithArray() {
		self::$instance->registerHook([
			$this->getMockBuilder('OCA\Encryption\Hooks\Contracts\IHook')->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder('OCA\Encryption\Hooks\Contracts\IHook')->disableOriginalConstructor()->getMock(),
			$this->getMock('NotIHook')
		]);

		$hookInstances = self::invokePrivate(self::$instance, 'hookInstances');
		// Make sure our type checking works
		$this->assertCount(2, $hookInstances);
	}


	/**
	 *
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		// have to make instance static to preserve data between tests
		self::$instance = new HookManager();

	}

	/**
	 *
	 */
	public function testRegisterHooksWithInstance() {
		$mock = $this->getMockBuilder('OCA\Encryption\Hooks\Contracts\IHook')->disableOriginalConstructor()->getMock();
		self::$instance->registerHook($mock);

		$hookInstances = self::invokePrivate(self::$instance, 'hookInstances');
		$this->assertCount(3, $hookInstances);

	}

}
