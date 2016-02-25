<?php

namespace OC\Preview;

class JPEG extends Image {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/image\/jpeg/';
	}
}
