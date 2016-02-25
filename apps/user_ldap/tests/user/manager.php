<?php

namespace OCA\user_ldap\tests;

use OCA\user_ldap\lib\user\Manager;

class Test_User_Manager extends \Test\TestCase {

	private function getTestInstances() {
		$access = $this->getMock('\OCA\user_ldap\lib\user\IUserTools');
		$config = $this->getMock('\OCP\IConfig');
		$filesys = $this->getMock('\OCA\user_ldap\lib\FilesystemHelper');
		$log = $this->getMock('\OCA\user_ldap\lib\LogWrapper');
		$avaMgr = $this->getMock('\OCP\IAvatarManager');
		$image = $this->getMock('\OCP\Image');
		$dbc = $this->getMock('\OCP\IDBConnection');

		return array($access, $config, $filesys, $image, $log, $avaMgr, $dbc);
	}

	public function testGetByDNExisting() {
		list($access, $config, $filesys, $image, $log, $avaMgr, $dbc) =
			$this->getTestInstances();

		$inputDN = 'cn=foo,dc=foobar,dc=bar';
		$uid = '563418fc-423b-1033-8d1c-ad5f418ee02e';

		$access->expects($this->once())
			->method('stringResemblesDN')
			->with($this->equalTo($inputDN))
			->will($this->returnValue(true));

		$access->expects($this->once())
			->method('dn2username')
			->with($this->equalTo($inputDN))
			->will($this->returnValue($uid));

		$access->expects($this->never())
			->method('username2dn');

		$manager = new Manager($config, $filesys, $log, $avaMgr, $image, $dbc);
		$manager->setLdapAccess($access);
		$user = $manager->get($inputDN);

		// Now we fetch the user again. If this leads to a failing test,
		// runtime caching the manager is broken.
		$user = $manager->get($inputDN);

		$this->assertInstanceOf('\OCA\user_ldap\lib\user\User', $user);
	}

	public function testGetByEDirectoryDN() {
		list($access, $config, $filesys, $image, $log, $avaMgr, $dbc) =
			$this->getTestInstances();

		$inputDN = 'uid=foo,o=foobar,c=bar';
		$uid = '563418fc-423b-1033-8d1c-ad5f418ee02e';

		$access->expects($this->once())
			->method('stringResemblesDN')
			->with($this->equalTo($inputDN))
			->will($this->returnValue(true));

		$access->expects($this->once())
			->method('dn2username')
			->with($this->equalTo($inputDN))
			->will($this->returnValue($uid));

		$access->expects($this->never())
			->method('username2dn');

		$manager = new Manager($config, $filesys, $log, $avaMgr, $image, $dbc);
		$manager->setLdapAccess($access);
		$user = $manager->get($inputDN);

		$this->assertInstanceOf('\OCA\user_ldap\lib\user\User', $user);
	}

	public function testGetByExoticDN() {
		list($access, $config, $filesys, $image, $log, $avaMgr, $dbc) =
			$this->getTestInstances();

		$inputDN = 'ab=cde,f=ghei,mno=pq';
		$uid = '563418fc-423b-1033-8d1c-ad5f418ee02e';

		$access->expects($this->once())
			->method('stringResemblesDN')
			->with($this->equalTo($inputDN))
			->will($this->returnValue(true));

		$access->expects($this->once())
			->method('dn2username')
			->with($this->equalTo($inputDN))
			->will($this->returnValue($uid));

		$access->expects($this->never())
			->method('username2dn');

		$manager = new Manager($config, $filesys, $log, $avaMgr, $image, $dbc);
		$manager->setLdapAccess($access);
		$user = $manager->get($inputDN);

		$this->assertInstanceOf('\OCA\user_ldap\lib\user\User', $user);
	}

	public function testGetByDNNotExisting() {
		list($access, $config, $filesys, $image, $log, $avaMgr, $dbc) =
			$this->getTestInstances();

		$inputDN = 'cn=gone,dc=foobar,dc=bar';

		$access->expects($this->once())
			->method('stringResemblesDN')
			->with($this->equalTo($inputDN))
			->will($this->returnValue(true));

		$access->expects($this->once())
			->method('dn2username')
			->with($this->equalTo($inputDN))
			->will($this->returnValue(false));

		$access->expects($this->once())
			->method('username2dn')
			->with($this->equalTo($inputDN))
			->will($this->returnValue(false));

		$manager = new Manager($config, $filesys, $log, $avaMgr, $image, $dbc);
		$manager->setLdapAccess($access);
		$user = $manager->get($inputDN);

		$this->assertNull($user);
	}

	public function testGetByUidExisting() {
		list($access, $config, $filesys, $image, $log, $avaMgr, $dbc) =
			$this->getTestInstances();

		$dn = 'cn=foo,dc=foobar,dc=bar';
		$uid = '563418fc-423b-1033-8d1c-ad5f418ee02e';

		$access->expects($this->never())
			->method('dn2username');

		$access->expects($this->once())
			->method('username2dn')
			->with($this->equalTo($uid))
			->will($this->returnValue($dn));

		$access->expects($this->once())
			->method('stringResemblesDN')
			->with($this->equalTo($uid))
			->will($this->returnValue(false));

		$manager = new Manager($config, $filesys, $log, $avaMgr, $image, $dbc);
		$manager->setLdapAccess($access);
		$user = $manager->get($uid);

		// Now we fetch the user again. If this leads to a failing test,
		// runtime caching the manager is broken.
		$user = $manager->get($uid);

		$this->assertInstanceOf('\OCA\user_ldap\lib\user\User', $user);
	}

	public function testGetByUidNotExisting() {
		list($access, $config, $filesys, $image, $log, $avaMgr, $dbc) =
			$this->getTestInstances();

		$dn = 'cn=foo,dc=foobar,dc=bar';
		$uid = 'gone';

		$access->expects($this->never())
			->method('dn2username');

		$access->expects($this->exactly(1))
			->method('username2dn')
			->with($this->equalTo($uid))
			->will($this->returnValue(false));

		$manager = new Manager($config, $filesys, $log, $avaMgr, $image, $dbc);
		$manager->setLdapAccess($access);
		$user = $manager->get($uid);

		$this->assertNull($user);
	}

}
