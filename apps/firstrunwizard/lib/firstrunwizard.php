<?php

namespace OCA_FirstRunWizard;

class Config {

	/**
	* @brief Disable the FirstRunWizard
	*/
	public static function enable() {
		\OCP\Config::setUserValue( \OCP\User::getUser(), 'firstrunwizard', 'show', 1 );
	}
	
	/**
	* @brief Enable the FirstRunWizard
	*/
	public static function disable() {
		\OCP\Config::setUserValue( \OCP\User::getUser(), 'firstrunwizard', 'show', 0 );
	}

	/**
	* @brief Check if the FirstRunWizard is enabled or not
	* @return bool
	*/
	public static function isenabled() {
		$conf=\OCP\CONFIG::getUserValue( \OCP\User::getUser() , 'firstrunwizard' , 'show' , 1 );
		if($conf==1) {
			return(true);
		}else{
			return(false);
		}
	}



}
