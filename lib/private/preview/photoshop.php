<?php

namespace OC\Preview;

//.psd
class Photoshop extends Bitmap {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/application\/x-photoshop/';
	}
}
