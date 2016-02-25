<?php

namespace OC\Preview;

class GIF extends Image {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/image\/gif/';
	}
}
