<?php
namespace OC\Preview;

class MarkDown extends TXT {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/text\/(x-)?markdown/';
	}

}
