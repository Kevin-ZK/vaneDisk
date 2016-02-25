<?php

namespace OC\Preview;

// .otf, .ttf and .pfb
class Font extends Bitmap {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/application\/(?:font-sfnt|x-font$)/';
	}
}
