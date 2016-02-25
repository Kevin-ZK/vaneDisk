<?php

namespace OC\Preview;

//.pdf
class PDF extends Bitmap {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/application\/pdf/';
	}
}
