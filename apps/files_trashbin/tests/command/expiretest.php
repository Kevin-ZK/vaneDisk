<?php

namespace OCA\Files_Trashbin\Tests\Command;

use OCA\Files_Trashbin\Command\Expire;
use Test\TestCase;

class ExpireTest extends TestCase {
	public function testExpireNonExistingUser() {
		$command = new Expire('test', 0);
		$command->handle();

		$this->assertTrue(true);
	}
}
