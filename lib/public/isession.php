<?php
/**
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

/**
 * Public interface of ownCloud for apps to use.
 * Session interface
 *
 */

// use OCP namespace for all classes that are considered public.
// This means that they should be used by apps instead of the internal ownCloud classes
namespace OCP;

/**
 * Interface ISession
 *
 * wrap PHP's internal session handling into the ISession interface
 * @since 6.0.0
 */
interface ISession {

	/**
	 * Set a value in the session
	 *
	 * @param string $key
	 * @param mixed $value
	 * @since 6.0.0
	 */
	public function set($key, $value);

	/**
	 * Get a value from the session
	 *
	 * @param string $key
	 * @return mixed should return null if $key does not exist
	 * @since 6.0.0
	 */
	public function get($key);

	/**
	 * Check if a named key exists in the session
	 *
	 * @param string $key
	 * @return bool
	 * @since 6.0.0
	 */
	public function exists($key);

	/**
	 * Remove a $key/$value pair from the session
	 *
	 * @param string $key
	 * @since 6.0.0
	 */
	public function remove($key);

	/**
	 * Reset and recreate the session
	 * @since 6.0.0
	 */
	public function clear();

	/**
	 * Close the session and release the lock
	 * @since 7.0.0
	 */
	public function close();

}
