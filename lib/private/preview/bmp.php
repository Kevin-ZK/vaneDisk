<?php

namespace OC\Preview;

class BMP extends Image {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/image\/bmp/';
	}
}
