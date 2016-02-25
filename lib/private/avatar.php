<?php

namespace OC;

use OC\Files\Filesystem;
use OC_Image;

/**
 * This class gets and sets users avatars.
 */

class Avatar implements \OCP\IAvatar {
	/** @var Files\View  */
	private $view;

	/**
	 * constructor
	 * @param string $user user to do avatar-management with
	 * @throws \Exception In case the username is potentially dangerous
	 */
	public function __construct ($user) {
		if(!Filesystem::isValidPath($user)) {
			throw new \Exception('Username may not contain slashes');
		}
		$this->view = new \OC\Files\View('/'.$user);
	}

	/**
	 * get the users avatar
	 * @param int $size size in px of the avatar, avatars are square, defaults to 64
	 * @return boolean|\OCP\IImage containing the avatar or false if there's no image
	*/
	public function get ($size = 64) {
		if ($this->view->file_exists('avatar.jpg')) {
			$ext = 'jpg';
		} elseif ($this->view->file_exists('avatar.png')) {
			$ext = 'png';
		} else {
			return false;
		}

		$avatar = new OC_Image();
		$avatar->loadFromData($this->view->file_get_contents('avatar.'.$ext));
		$avatar->resize($size);
		return $avatar;
	}

	/**
	 * Check if an avatar exists for the user
	 *
	 * @return bool
	 */
	public function exists() {
		return $this->view->file_exists('avatar.jpg') || $this->view->file_exists('avatar.png');
	}

	/**
	 * sets the users avatar
	 * @param \OCP\IImage|resource|string $data An image object, imagedata or path to set a new avatar
	 * @throws \Exception if the provided file is not a jpg or png image
	 * @throws \Exception if the provided image is not valid
	 * @throws \OC\NotSquareException if the image is not square
	 * @return void
	*/
	public function set ($data) {

		if($data instanceOf \OCP\IImage) {
			$img = $data;
			$data = $img->data();
		} else {
			$img = new OC_Image($data);
		}
		$type = substr($img->mimeType(), -3);
		if ($type === 'peg') {
			$type = 'jpg';
		}
		if ($type !== 'jpg' && $type !== 'png') {
			$l = \OC::$server->getL10N('lib');
			throw new \Exception($l->t("Unknown filetype"));
		}

		if (!$img->valid()) {
			$l = \OC::$server->getL10N('lib');
			throw new \Exception($l->t("Invalid image"));
		}

		if (!($img->height() === $img->width())) {
			throw new \OC\NotSquareException();
		}

		$this->view->unlink('avatar.jpg');
		$this->view->unlink('avatar.png');
		$this->view->file_put_contents('avatar.'.$type, $data);
	}

	/**
	 * remove the users avatar
	 * @return void
	*/
	public function remove () {
		$this->view->unlink('avatar.jpg');
		$this->view->unlink('avatar.png');
	}
}
