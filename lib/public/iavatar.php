<?php

namespace OCP;

/**
 * This class provides avatar functionality
 * @since 6.0.0
 */
interface IAvatar {

	/**
	 * get the users avatar
	 * @param int $size size in px of the avatar, avatars are square, defaults to 64
	 * @return boolean|\OCP\IImage containing the avatar or false if there's no image
	 * @since 6.0.0
	 */
	public function get($size = 64);

	/**
	 * Check if an avatar exists for the user
	 *
	 * @return bool
	 * @since 8.1.0
	 */
	public function exists();

	/**
	 * sets the users avatar
	 * @param \OCP\IImage|resource|string $data An image object, imagedata or path to set a new avatar
	 * @throws \Exception if the provided file is not a jpg or png image
	 * @throws \Exception if the provided image is not valid
	 * @throws \OC\NotSquareException if the image is not square
	 * @return void
	 * @since 6.0.0
	 */
	public function set($data);

	/**
	 * remove the users avatar
	 * @return void
	 * @since 6.0.0
	 */
	public function remove();
}
