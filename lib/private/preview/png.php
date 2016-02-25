<?php

namespace OC\Preview;

class PNG extends Image {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/image\/png/';
	}
}
