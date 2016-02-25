<?php

namespace OC\Files\Mount;

/**
 * Defines the mount point to be (re)moved by the user
 */
interface MoveableMount {
	/**
	 * Move the mount point to $target
	 *
	 * @param string $target the target mount point
	 * @return bool
	 */
	public function moveMount($target);

	/**
	 * Remove the mount points
	 *
	 * @return mixed
	 * @return bool
	 */
	public function removeMount();
}
