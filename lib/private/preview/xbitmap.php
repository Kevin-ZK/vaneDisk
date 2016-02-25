<?php

namespace OC\Preview;

class XBitmap extends Image {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/image\/x-xbitmap/';
	}
}
