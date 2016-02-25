<?php

class OC_Log_Errorlog {


	/**
	 * Init class data
	 */
	public static function init() {
	}

	/**
	 * write a message in the log
	 * @param string $app
	 * @param string $message
	 * @param int $level
	 */
	public static function write($app, $message, $level) {
		error_log('[vanedisk]['.$app.']['.$level.'] '.$message);
	}
}

