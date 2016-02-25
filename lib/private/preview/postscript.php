<?php

namespace OC\Preview;

//.eps
class Postscript extends Bitmap {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/application\/postscript/';
	}
}
