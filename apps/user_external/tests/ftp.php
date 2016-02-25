<?php

class Test_User_FTP extends \Test\TestCase {
	/**
	 * @var OC_User_IMAP $instance
	 */
	private $instance;

	private function getConfig() {
		return include(__DIR__.'/config.php');
	}

	function skip() {
		$config=$this->getConfig();
		$this->skipUnless($config['ftp']['run']);
	}

	protected function setUp() {
		parent::setUp();
		$config=$this->getConfig();
		$this->instance=new OC_User_FTP($config['ftp']['host']);
	}

	function testLogin() {
		$config=$this->getConfig();
		$this->assertEquals($config['ftp']['user'],$this->instance->checkPassword($config['ftp']['user'],$config['ftp']['password']));
		$this->assertFalse($this->instance->checkPassword($config['ftp']['user'],$config['ftp']['password'].'foo'));
	}
}
