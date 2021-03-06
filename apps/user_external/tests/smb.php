<?php

class Test_User_SMB extends \Test\TestCase {
	/**
	 * @var OC_User_IMAP $instance
	 */
	private $instance;

	private function getConfig() {
		return include(__DIR__.'/config.php');
	}

	function skip() {
		$config=$this->getConfig();
		$this->skipUnless($config['smb']['run']);
	}

	protected function setUp() {
		parent::setUp();

		$config=$this->getConfig();
		$this->instance=new OC_User_SMB($config['smb']['host']);
	}

	function testLogin() {
		$config=$this->getConfig();
		$this->assertEquals($config['smb']['user'],$this->instance->checkPassword($config['smb']['user'],$config['smb']['password']));
		$this->assertFalse($this->instance->checkPassword($config['smb']['user'],$config['smb']['password'].'foo'));
	}
}
