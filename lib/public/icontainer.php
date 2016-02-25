<?php
namespace OCP;

/**
 * Class IContainer
 *
 * IContainer is the basic interface to be used for any internal dependency injection mechanism
 *
 * @package OCP
 * @since 6.0.0
 */
interface IContainer {

	/**
	 * Look up a service for a given name in the container.
	 *
	 * @param string $name
	 * @return mixed
	 * @since 6.0.0
	 */
	public function query($name);

	/**
	 * A value is stored in the container with it's corresponding name
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 * @since 6.0.0
	 */
	public function registerParameter($name, $value);

	/**
	 * A service is registered in the container where a closure is passed in which will actually
	 * create the service on demand.
	 * In case the parameter $shared is set to true (the default usage) the once created service will remain in
	 * memory and be reused on subsequent calls.
	 * In case the parameter is false the service will be recreated on every call.
	 *
	 * @param string $name
	 * @param \Closure $closure
	 * @param bool $shared
	 * @return void
	 * @since 6.0.0
	 */
	public function registerService($name, \Closure $closure, $shared = true);
}
