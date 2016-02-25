<?php

class OC_OCS_Person {

	public static function check() {
		$login = isset($_POST['login']) ? $_POST['login'] : false;
		$password = isset($_POST['password']) ? $_POST['password'] : false;
		if($login && $password) {
			if(OC_User::checkPassword($login, $password)) {
				$xml['person']['personid'] = $login;
				return new OC_OCS_Result($xml);
			} else {
				return new OC_OCS_Result(null, 102);
			}
		} else {
			return new OC_OCS_Result(null, 101);
		}
	}

}
