<?php
namespace OCP;

/**
 * Interface IUserBackend
 *
 * @package OCP
 * @since 8.0.0
 */
interface IUserBackend {

	/**
	 * Backend name to be shown in user management
	 * @return string the name of the backend to be shown
	 * @since 8.0.0
	 */
	public function getBackendName();

}
