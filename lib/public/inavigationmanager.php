<?php
namespace OCP;

/**
 * Manages the vanedisk navigation
 * @since 6.0.0
 */
interface INavigationManager {
	/**
	 * Creates a new navigation entry
	 *
	 * @param array|\Closure $entry Array containing: id, name, order, icon and href key
	 *					The use of a closure is preferred, because it will avoid
	 * 					loading the routing of your app, unless required.
	 * @return void
	 * @since 6.0.0
	 */
	public function add($entry);

	/**
	 * Sets the current navigation entry of the currently running app
	 * @param string $appId id of the app entry to activate (from added $entry)
	 * @return void
	 * @since 6.0.0
	 */
	public function setActiveEntry($appId);
}
