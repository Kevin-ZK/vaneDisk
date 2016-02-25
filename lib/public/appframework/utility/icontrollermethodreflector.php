<?php

namespace OCP\AppFramework\Utility;

/**
 * Interface ControllerMethodReflector
 *
 * Reads and parses annotations from doc comments
 *
 * @package OCP\AppFramework\Utility
 * @since 8.0.0
 */
interface IControllerMethodReflector {

	/**
	 * @param object $object an object or classname
	 * @param string $method the method which we want to inspect
	 * @since 8.0.0
	 */
	public function reflect($object, $method);

	/**
	 * Inspects the PHPDoc parameters for types
	 *
	 * @param string $parameter the parameter whose type comments should be
	 * parsed
	 * @return string|null type in the type parameters (@param int $something)
	 * would return int or null if not existing
	 * @since 8.0.0
	 */
	public function getType($parameter);

	/**
	 * @return array the arguments of the method with key => default value
	 * @since 8.0.0
	 */
	public function getParameters();

	/**
	 * Check if a method contains an annotation
	 *
	 * @param string $name the name of the annotation
	 * @return bool true if the annotation is found
	 * @since 8.0.0
	 */
	public function hasAnnotation($name);

}
