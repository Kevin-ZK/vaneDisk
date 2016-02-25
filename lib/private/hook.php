<?php
class OC_Hook{
	public static $thrownExceptions = [];

	static private $registered = array();

	/**
	 * connects a function to a hook
	 *
	 * @param string $signalClass class name of emitter
	 * @param string $signalName name of signal
	 * @param string|object $slotClass class name of slot
	 * @param string $slotName name of slot
	 * @return bool
	 *
	 * This function makes it very easy to connect to use hooks.
	 *
	 * TODO: write example
	 */
	static public function connect($signalClass, $signalName, $slotClass, $slotName ) {
		// If we're trying to connect to an emitting class that isn't
		// yet registered, register it
		if( !array_key_exists($signalClass, self::$registered )) {
			self::$registered[$signalClass] = array();
		}
		// If we're trying to connect to an emitting method that isn't
		// yet registered, register it with the emitting class
		if( !array_key_exists( $signalName, self::$registered[$signalClass] )) {
			self::$registered[$signalClass][$signalName] = array();
		}

		// dont connect hooks twice
		foreach (self::$registered[$signalClass][$signalName] as $hook) {
			if ($hook['class'] === $slotClass and $hook['name'] === $slotName) {
				return false;
			}
		}
		// Connect the hook handler to the requested emitter
		self::$registered[$signalClass][$signalName][] = array(
				"class" => $slotClass,
				"name" => $slotName
		);

		// No chance for failure ;-)
		return true;
	}

	/**
	 * emits a signal
	 *
	 * @param string $signalClass class name of emitter
	 * @param string $signalName name of signal
	 * @param mixed $params default: array() array with additional data
	 * @return bool true if slots exists or false if not
	 *
	 * Emits a signal. To get data from the slot use references!
	 *
	 * TODO: write example
	 */
	static public function emit($signalClass, $signalName, $params = array()) {

		// Return false if no hook handlers are listening to this
		// emitting class
		if( !array_key_exists($signalClass, self::$registered )) {
			return false;
		}

		// Return false if no hook handlers are listening to this
		// emitting method
		if( !array_key_exists( $signalName, self::$registered[$signalClass] )) {
			return false;
		}

		// Call all slots
		foreach( self::$registered[$signalClass][$signalName] as $i ) {
			try {
				call_user_func( array( $i["class"], $i["name"] ), $params );
			} catch (Exception $e){
				self::$thrownExceptions[] = $e;
				$class = $i["class"];
				if (is_object($i["class"])) {
					$class = get_class($i["class"]);
				}
				$message = $e->getMessage();
				if (empty($message)) {
					$message = get_class($e);
				}
				OC_Log::write('hook',
					'error while running hook (' . $class . '::' . $i["name"] . '): ' . $message,
					OC_Log::ERROR);
				if($e instanceof \OC\ServerNotAvailableException) {
					throw $e;
				}
			}
		}

		// return true
		return true;
	}

	/**
	 * clear hooks
	 * @param string $signalclass
	 * @param string $signalname
	 */
	static public function clear($signalclass='', $signalname='') {
		if($signalclass) {
			if($signalname) {
				self::$registered[$signalclass][$signalname]=array();
			}else{
				self::$registered[$signalclass]=array();
			}
		}else{
			self::$registered=array();
		}
	}

	/**
	 * DO NOT USE!
	 * For unit tests ONLY!
	 */
	static public function getHooks() {
		return self::$registered;
	}
}
