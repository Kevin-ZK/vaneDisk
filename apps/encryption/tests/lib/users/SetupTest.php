<?php

namespace OCA\Encryption\Tests\Users;


use OCA\Encryption\Users\Setup;
use Test\TestCase;

class SetupTest extends TestCase {
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	private $keyManagerMock;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	private $cryptMock;
	/**
	 * @var Setup
	 */
	private $instance;

	public function testSetupServerSide() {
		$this->keyManagerMock->expects($this->exactly(2))
			->method('userHasKeys')
			->with('admin')
			->willReturnOnConsecutiveCalls(true, false);

		$this->assertTrue($this->instance->setupServerSide('admin',
			'password'));

		$this->keyManagerMock->expects($this->once())
			->method('storeKeyPair')
			->with('admin', 'password')
			->willReturn(false);

		$this->assertFalse($this->instance->setupServerSide('admin',
			'password'));
	}

	protected function setUp() {
		parent::setUp();
		$logMock = $this->getMock('OCP\ILogger');
		$userSessionMock = $this->getMockBuilder('OCP\IUserSession')
			->disableOriginalConstructor()
			->getMock();
		$this->cryptMock = $this->getMockBuilder('OCA\Encryption\Crypto\Crypt')
			->disableOriginalConstructor()
			->getMock();

		$this->keyManagerMock = $this->getMockBuilder('OCA\Encryption\KeyManager')
			->disableOriginalConstructor()
			->getMock();

		$this->instance = new Setup($logMock,
			$userSessionMock,
			$this->cryptMock,
			$this->keyManagerMock);
	}

}
