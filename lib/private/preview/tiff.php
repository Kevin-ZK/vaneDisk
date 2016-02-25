<?php

namespace OC\Preview;

//.tiff
class TIFF extends Bitmap {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/image\/tiff/';
	}
}
