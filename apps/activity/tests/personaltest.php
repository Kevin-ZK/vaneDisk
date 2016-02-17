<?php

namespace OCA\Activity\Tests;

class PersonalTest extends TestCase {
	public function testInclude() {
		$settingsPage = include '../personal.php';
		$this->assertNotEmpty(include '../personal.php', 'Asserting that the personal.php does produce output.');
	}
}
