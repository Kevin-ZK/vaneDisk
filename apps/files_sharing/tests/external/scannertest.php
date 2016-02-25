<?php

namespace OCA\Files_Sharing\Tests\External;

use OCA\Files_Sharing\External\Scanner;
use Test\TestCase;

class ScannerTest extends TestCase {
	/** @var \OCA\Files_Sharing\External\Scanner */
	protected $scanner;
	/** @var \OCA\Files_Sharing\External\Storage|\PHPUnit_Framework_MockObject_MockObject */
	protected $storage;
	/** @var \OC\Files\Cache\Cache|\PHPUnit_Framework_MockObject_MockObject */
	protected $cache;

	protected function setUp() {
		parent::setUp();

		$this->storage = $this->getMockBuilder('\OCA\Files_Sharing\External\Storage')
			->disableOriginalConstructor()
			->getMock();
		$this->cache = $this->getMockBuilder('\OC\Files\Cache\Cache')
			->disableOriginalConstructor()
			->getMock();
		$this->storage->expects($this->any())
			->method('getCache')
			->willReturn($this->cache);

		$this->scanner = new Scanner($this->storage);
	}

	public function testScanAll() {
		$this->storage->expects($this->any())
			->method('getShareInfo')
			->willReturn(['status' => 'success', 'data' => []]);

		// FIXME add real tests, we are currently only checking for
		// Declaration of OCA\Files_Sharing\External\Scanner::*() should be
		// compatible with OC\Files\Cache\Scanner::*()
		$this->scanner->scanAll();
		$this->assertTrue(true);
	}

	public function testScan() {
		$this->storage->expects($this->any())
			->method('getShareInfo')
			->willReturn(['status' => 'success', 'data' => []]);

		// FIXME add real tests, we are currently only checking for
		// Declaration of OCA\Files_Sharing\External\Scanner::*() should be
		// compatible with OC\Files\Cache\Scanner::*()
		$this->scanner->scan('test', Scanner::SCAN_RECURSIVE);
		$this->assertTrue(true);
	}

	public function testScanFile() {
		// FIXME add real tests, we are currently only checking for
		// Declaration of OCA\Files_Sharing\External\Scanner::*() should be
		// compatible with OC\Files\Cache\Scanner::*()
		$this->scanner->scanFile('test', Scanner::SCAN_RECURSIVE);
		$this->assertTrue(true);
	}
}
