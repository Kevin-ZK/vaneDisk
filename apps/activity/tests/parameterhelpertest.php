<?php

namespace OCA\Activity\Tests;

use OC\ActivityManager;
use OCA\Activity\Tests\Mock\Extension;

class ParameterHelperTest extends TestCase {
	/** @var string */
	protected $originalWEBROOT;
	/** @var \OCA\Activity\ParameterHelper */
	protected $parameterHelper;
	/** @var \PHPUnit_Framework_MockObject_MockObject */
	protected $view;
	/** @var \PHPUnit_Framework_MockObject_MockObject */

	/** @var \PHPUnit_Framework_MockObject_MockObject */
	protected $config;

	/** @var \PHPUnit_Framework_MockObject_MockObject */
	protected $userManager;

	protected function setUp() {
		parent::setUp();

		$this->originalWEBROOT =\OC::$WEBROOT;
		\OC::$WEBROOT = '';
		$this->view = $view = $this->getMockBuilder('\OC\Files\View')
			->disableOriginalConstructor()
			->getMock();
		$this->config = $this->getMockBuilder('\OCP\IConfig')
			->disableOriginalConstructor()
			->getMock();
		$activityLanguage = \OCP\Util::getL10N('activity', 'en');
		$activityManager = new ActivityManager(
			$this->getMock('OCP\IRequest'),
			$this->getMock('OCP\IUserSession'),
			$this->getMock('OCP\IConfig')
		);
		$activityManager->registerExtension(function() use ($activityLanguage) {
			return new Extension($activityLanguage, $this->getMock('\OCP\IURLGenerator'));
		});
		$this->userManager = $this->getMock('OCP\IUserManager');
		$this->userManager->expects($this->any())
			->method('get')
			->willReturnMap([
				['user1', $this->getUserMockDisplayName('user1', 'User One')],
				['user2', $this->getUserMockDisplayName('user1', 'User Two')],
				['user<HTML>', $this->getUserMockDisplayName('user<HTML>', 'User <HTML>')],
			]);

		/** @var \OC\Files\View $view */
		$this->parameterHelper = new \OCA\Activity\ParameterHelper(
			$activityManager,
			$this->userManager,
			$view,
			$this->config,
			$activityLanguage,
			'test'
		);
	}

	protected function getUserMockDisplayName($uid, $displayName) {
		$mock = $this->getMock('OCP\IUser');
		$mock->expects($this->any())
			->method('getUID')
			->willReturn($uid);
		$mock->expects($this->any())
			->method('getDisplayName')
			->willReturn($displayName);
		return $mock;
	}

	protected function tearDown() {
		\OC::$WEBROOT = $this->originalWEBROOT;
		parent::tearDown();
	}

	public function prepareParametersData() {
		return array(
			array(array(), false, false, false, array()),

			// No file position: no path strip
			array(array('/foo/bar.file'), array(), false, false, array('/foo/bar.file')),
			array(array('/foo/bar.file'), array(), true, false, array('/foo/bar.file')),
			array(array('/foo/bar.file'), array(), false, true, array('<strong>/foo/bar.file</strong>')),
			array(array('/foo/bar.file'), array(), true, true, array('<strong>/foo/bar.file</strong>')),

			// Valid file position
			array(array('/foo/bar.file'), array(0 => 'file'), true, false, array('bar.file')),
			array(array('/folder/trailingslash/fromsharing/'), array(0 => 'file'), true, false, array('fromsharing')),
			array(array('/foo/bar.file'), array(0 => 'file'), false, false, array('foo/bar.file')),
			array(array('/folder/trailingslash/fromsharing/'), array(0 => 'file'), false, false, array('folder/trailingslash/fromsharing')),
			array(array('/foo/bar.file'), array(0 => 'file'), true, true, array(
				'<a class="filename tooltip" href="/index.php/apps/files?dir=%2Ffoo&scrollto=bar.file" title="in foo">bar.file</a>',
			)),
			array(array('/0/bar.file'), array(0 => 'file'), true, true, array(
				'<a class="filename tooltip" href="/index.php/apps/files?dir=%2F0&scrollto=bar.file" title="in 0">bar.file</a>',
			)),
			array(array('/foo/bar.file'), array(1 => 'file'), true, false, array('/foo/bar.file')),
			array(array('/foo/bar.file'), array(1 => 'file'), true, true, array('<strong>/foo/bar.file</strong>')),

			// Legacy: stored without leading slash
			array(array('foo/bar.file'), array(0 => 'file'), false, false, array('foo/bar.file')),
			array(array('foo/bar.file'), array(0 => 'file'), false, true, array(
				'<a class="filename" href="/index.php/apps/files?dir=%2Ffoo&scrollto=bar.file">foo/bar.file</a>',
			)),
			array(array('foo/bar.file'), array(0 => 'file'), true, false, array('bar.file')),
			array(array('foo/bar.file'), array(0 => 'file'), true, true, array(
				'<a class="filename tooltip" href="/index.php/apps/files?dir=%2Ffoo&scrollto=bar.file" title="in foo">bar.file</a>',
			)),

			// Valid file position
			array(array('UserA', '/foo/bar.file'), array(1 => 'file'), true, false, array('UserA', 'bar.file')),
			array(array('UserA', '/foo/bar.file'), array(1 => 'file'), true, true, array(
				'<strong>UserA</strong>',
				'<a class="filename tooltip" href="/index.php/apps/files?dir=%2Ffoo&scrollto=bar.file" title="in foo">bar.file</a>',
			)),
			array(array('UserA', '/foo/bar.file'), array(2 => 'file'), true, false, array('UserA', '/foo/bar.file')),
			array(array('UserA', '/foo/bar.file'), array(2 => 'file'), true, true, array(
				'<strong>UserA</strong>',
				'<strong>/foo/bar.file</strong>',
			)),
			array(array('user1', '/foo/bar.file'), array(0 => 'username'), true, true, array(
				'<div class="avatar" data-user="user1"></div><strong>User One</strong>',
				'<strong>/foo/bar.file</strong>',
			)),
			// Test HTML escape
			array(array('user<HTML>', '/foo/bar.file'), array(0 => 'username'), true, true, array(
				'<div class="avatar" data-user="user&lt;HTML&gt;"></div><strong>User &lt;HTML&gt;</strong>',
				'<strong>/foo/bar.file</strong>',
			)),
			array(array('', '/foo/bar.file'), array(0 => 'username'), true, true, array(
				'<strong>"remote user"</strong>',
				'<strong>/foo/bar.file</strong>',
			)),
			array(array('', '/foo/bar.file'), array(0 => 'username'), true, false, array(
				'"remote user"',
				'/foo/bar.file',
			)),

			array(array('user1', '/foo/bar.file'), array(0 => 'username', 1 => 'file'), true, true, array(
				'<div class="avatar" data-user="user1"></div><strong>User One</strong>',
				'<a class="filename tooltip" href="/index.php/apps/files?dir=%2Ffoo&scrollto=bar.file" title="in foo">bar.file</a>',
			)),
			array(array('user1', '/tmp/test'), array(0 => 'username', 1 => 'file'), true, true, array(
				'<div class="avatar" data-user="user1"></div><strong>User One</strong>',
				'<a class="filename tooltip" href="/index.php/apps/files?dir=%2Ftmp%2Ftest" title="in tmp">test</a>',
			), '/test/files/tmp/test'),

			// Disabled avatars #256
			array(array('NoAvatar'), array(0 => 'username'), true, true, array(
				'<strong>NoAvatar</strong>',
			), '', false),
		);
	}

	/**
	 * @dataProvider prepareParametersData
	 */
	public function testPrepareParameters($params, $filePosition, $stripPath, $highlightParams, $expected, $createFolder = '', $enableAvatars = true) {
		if ($createFolder !== '') {
			$this->view->expects($this->any())
				->method('is_dir')
				->with($createFolder)
				->willReturn(true);
		}

		$this->config->expects($this->any())
			->method('getSystemValue')
			->with('enable_avatars', true)
			->willReturn($enableAvatars);

		$this->assertEquals(
			$expected,
			$this->parameterHelper->prepareParameters($params, $filePosition, $stripPath, $highlightParams)
		);
	}

	public function prepareArrayParametersData() {
		$en = \OCP\Util::getL10N('activity', 'en');
		$de = \OCP\Util::getL10N('activity', 'de');
		return array(
			array(array(), 'file', true, true, null, ''),
			array(array('A/B.txt', 'C/D.txt'), 'file', true, false, null, (string) $en->t('%s and %s', ['B.txt', 'D.txt'])),
			array(array('A/B.txt', 'C/D.txt'), 'file', true, false, $de, (string) $de->t('%s and %s', ['B.txt', 'D.txt'])),
			array(array('user1', 'user2'), 'username', true, false, null, (string) $en->t('%s and %s', ['User One', 'User Two'])),
			array(array('user1', 'user2'), 'username', true, false, $de, (string) $de->t('%s and %s', ['User One', 'User Two'])),
			array(array('A/B.txt', 'C/D.txt'), '', true, false, null, (string) $en->t('%s and %s', ['A/B.txt', 'C/D.txt'])),
			array(array('A/B.txt', 'C/D.txt'), '', true, false, $de, (string) $de->t('%s and %s', ['A/B.txt', 'C/D.txt'])),
			array(
				array('A/B.txt', 'C/D.txt', 'E/F.txt', 'G/H.txt', 'I/J.txt', 'K/L.txt', 'M/N.txt'), 'file', true, false, null,
				(string) $en->n('%s and %n more', '%s and %n more', 4, [implode((string) $en->t(', '), ['B.txt', 'D.txt', 'F.txt'])])
			),
			array(
				array('A/B.txt', 'C/D.txt', 'E/F.txt', 'G/H.txt', 'I/J.txt', 'K/L.txt', 'M/N.txt'), 'file', true, true, null,
				(string) $en->n(
					'%s and <strong class="tooltip" title="%s">%n more</strong>',
					'%s and <strong class="tooltip" title="%s">%n more</strong>',
					4,
					[
						implode((string) $en->t(', '), [
							'<a class="filename tooltip" href="/index.php/apps/files?dir=%2FA&scrollto=B.txt" title="in A">B.txt</a>',
							'<a class="filename tooltip" href="/index.php/apps/files?dir=%2FC&scrollto=D.txt" title="in C">D.txt</a>',
							'<a class="filename tooltip" href="/index.php/apps/files?dir=%2FE&scrollto=F.txt" title="in E">F.txt</a>',
						]),
						'G/H.txt, I/J.txt, K/L.txt, M/N.txt',
					])
			),
			array(
				array('A"><h1>/B.txt"><h1>', 'C"><h1>/D.txt"><h1>', 'E"><h1>/F.txt"><h1>', 'G"><h1>/H.txt"><h1>', 'I"><h1>/J.txt"><h1>', 'K"><h1>/L.txt"><h1>', 'M"><h1>/N.txt"><h1>'), 'file', true, true, null,
				(string) $en->n(
					'%s and <strong class="tooltip" title="%s">%n more</strong>',
					'%s and <strong class="tooltip" title="%s">%n more</strong>',
					4,
					[
						implode((string) $en->t(', '), [
							'<a class="filename tooltip" href="/index.php/apps/files?dir=%2FA%22%3E%3Ch1%3E&scrollto=B.txt%22%3E%3Ch1%3E" title="in A&quot;&gt;&lt;h1&gt;">B.txt&quot;&gt;&lt;h1&gt;</a>',
							'<a class="filename tooltip" href="/index.php/apps/files?dir=%2FC%22%3E%3Ch1%3E&scrollto=D.txt%22%3E%3Ch1%3E" title="in C&quot;&gt;&lt;h1&gt;">D.txt&quot;&gt;&lt;h1&gt;</a>',
							'<a class="filename tooltip" href="/index.php/apps/files?dir=%2FE%22%3E%3Ch1%3E&scrollto=F.txt%22%3E%3Ch1%3E" title="in E&quot;&gt;&lt;h1&gt;">F.txt&quot;&gt;&lt;h1&gt;</a>',
						]),
						'G&quot;&gt;&lt;h1&gt;/H.txt&quot;&gt;&lt;h1&gt;, I&quot;&gt;&lt;h1&gt;/J.txt&quot;&gt;&lt;h1&gt;, K&quot;&gt;&lt;h1&gt;/L.txt&quot;&gt;&lt;h1&gt;, M&quot;&gt;&lt;h1&gt;/N.txt&quot;&gt;&lt;h1&gt;',
					])
			),
		);
	}

	/**
	 * @dataProvider prepareArrayParametersData
	 */
	public function testPrepareArrayParameters($params, $paramType, $stripPath, $highlightParams, $l, $expected) {
		if ($l) {
			$this->parameterHelper->setL10n($l);
		}
		$this->assertEquals(
			$expected,
			(string) $this->parameterHelper->prepareArrayParameter($params, $paramType, $stripPath, $highlightParams)
		);
	}

	public function getSpecialParameterListData() {
		return array(
			array('app1', 'subject1', array(0 => 'file')),
			array('app1', 'subject2', array(0 => 'file', 1 => 'username')),
			array('app1', '', array()),
			array('calendar', 'shared_group', array()),
			array('calendar', '', array()),
		);
	}

	/**
	 * @dataProvider getSpecialParameterListData
	 */
	public function testGetSpecialParameterList($app, $text, $expected) {
		$this->assertEquals($expected, $this->parameterHelper->getSpecialParameterList($app, $text));
	}
}
