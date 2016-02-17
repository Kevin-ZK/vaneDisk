<?php

namespace OCA\Activity\Tests;

use OC\ActivityManager;
use OCA\Activity\Navigation;
use OCA\Activity\Tests\Mock\Extension;

class NavigationTest extends TestCase {
	public function getTemplateData() {
		return array(
			array('all', null),
			array('all', 'self'),
			array('random', null),
		);
	}

	/**
	 * @dataProvider getTemplateData
	 */
	public function testGetTemplate($constructorActive, $forceActive) {
		$activityLanguage = \OCP\Util::getL10N('activity', 'en');
		$activityManager = new ActivityManager(
			$this->getMock('OCP\IRequest'),
			$this->getMock('OCP\IUserSession'),
			$this->getMock('OCP\IConfig')
		);
		$activityManager->registerExtension(function() use ($activityLanguage) {
			return new Extension($activityLanguage, $this->getMock('\OCP\IURLGenerator'));
		});
		$userSettings = $this->getMockBuilder('OCA\Activity\UserSettings')
			->disableOriginalConstructor()
			->getMock();
		$userSettings->expects($this->exactly(2))
			->method('getUserSetting')
			->with('test', 'setting', 'self')
			->willReturn(true);
		$navigation = new Navigation(
			$activityLanguage,
			$activityManager,
			\OC::$server->getURLGenerator(),
			$userSettings,
			'test',
			'',
			$constructorActive
		);
		$output = $navigation->getTemplate($forceActive)->fetchPage();

		// Get only the template part with the navigation links
		$navigationLinks = substr($output, strpos($output, '<ul>') + 4);
		$navigationLinks = substr($navigationLinks, 0, strrpos($navigationLinks, '</li>'));

		// Remove tabs and new lines
		$navigationLinks = str_replace(array("\t", "\n"), '', $navigationLinks);

		// Turn the list of links into an array
		$navigationEntries = explode('</li>', $navigationLinks);

		$links = $navigation->getLinkList();

		// Check whether all top links are available
		foreach ($links['top'] as $link) {
			$found = false;
			foreach ($navigationEntries as $navigationEntry) {
				if (strpos($navigationEntry, 'data-navigation="' . $link['id'] . '"') !== false) {
					$found = true;
					$this->assertContains(
						'href="' . $link['url'] . '">' . $link['name']. '</a>',
						$navigationEntry
					);
					if ($forceActive == $link['id'] || ($forceActive == null && $constructorActive == $link['id'])) {
						$this->assertStringStartsWith('<li class="active">', $navigationEntry);
					} else {
						$this->assertStringStartsWith('<li>', $navigationEntry);
					}
				}
			}
			$this->assertTrue($found, 'Could not find navigation entry "' . $link['name'] . '"');
		}

		// Check size of app links
		$this->assertSame(1, sizeof($links['apps']));
		$this->assertNotContains('data-navigation="files"', $navigationLinks, 'Files app should not be included when there are no other apps.');
	}

}
