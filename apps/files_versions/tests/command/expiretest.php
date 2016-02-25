<?php

namespace OCA\Files_Versions\Tests\Command;

use OCA\Files_Versions\Command\Expire;
use Test\TestCase;

class ExpireTest extends TestCase {
	public function testExpireNonExistingUser() {
		$command = new Expire('test', '');
		$command->handle();

		$this->assertTrue(true);
	}
}
