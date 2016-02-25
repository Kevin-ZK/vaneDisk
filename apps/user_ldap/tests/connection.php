<?php

namespace OCA\user_ldap\tests;

class Test_Connection extends \Test\TestCase {

	public function testOriginalAgentUnchangedOnClone() {
		//background: upon login a bind is done with the user credentials
		//which is valid for the whole LDAP resource. It needs to be reset
		//to the agent's credentials
		$lw  = $this->getMock('\OCA\user_ldap\lib\ILDAPWrapper');

		$connection = new \OCA\user_ldap\lib\Connection($lw, '', null);
		$agent = array(
			'ldapAgentName' => 'agent',
			'ldapAgentPassword' => '123456',
		);
		$connection->setConfiguration($agent);

		$testConnection = clone $connection;
		$user = array(
			'ldapAgentName' => 'user',
			'ldapAgentPassword' => 'password',
		);
		$testConnection->setConfiguration($user);

		$agentName = $connection->ldapAgentName;
		$agentPawd = $connection->ldapAgentPassword;

		$this->assertSame($agentName, $agent['ldapAgentName']);
		$this->assertSame($agentPawd, $agent['ldapAgentPassword']);
	}

}
