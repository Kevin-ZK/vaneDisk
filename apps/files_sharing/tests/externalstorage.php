<?php

/**
 * Tests for the external Storage class for remote shares.
 */
class Test_Files_Sharing_External_Storage extends \Test\TestCase {

	function optionsProvider() {
		return array(
			array(
				'http://remoteserver:8080/owncloud',
				'http://remoteserver:8080/owncloud/public.php/webdav/',
			),
			// extra slash
			array(
				'http://remoteserver:8080/owncloud/',
				'http://remoteserver:8080/owncloud/public.php/webdav/',
			),
			// extra path
			array(
				'http://remoteserver:8080/myservices/owncloud/',
				'http://remoteserver:8080/myservices/owncloud/public.php/webdav/',
			),
			// root path
			array(
				'http://remoteserver:8080/',
				'http://remoteserver:8080/public.php/webdav/',
			),
			// without port
			array(
				'http://remoteserver/oc.test',
				'http://remoteserver/oc.test/public.php/webdav/',
			),
			// https
			array(
				'https://remoteserver/',
				'https://remoteserver/public.php/webdav/',
			),
		);
	}

	/**
	 * @dataProvider optionsProvider
	 */
	public function testStorageMountOptions($inputUri, $baseUri) {
		$certificateManager = \OC::$server->getCertificateManager();
		$storage = new TestSharingExternalStorage(
			array(
				'remote' => $inputUri,
				'owner' => 'testOwner',
				'mountpoint' => 'remoteshare',
				'token' => 'abcdef',
				'password' => '',
				'manager' => null,
				'certificateManager' => $certificateManager
			)
		);
		$this->assertEquals($baseUri, $storage->getBaseUri());
	}
}

/**
 * Dummy subclass to make it possible to access private members
 */
class TestSharingExternalStorage extends \OCA\Files_Sharing\External\Storage {

	public function getBaseUri() {
		return $this->createBaseUri();
	}
}
