<?php
namespace OC\Preview;

//.doc, .dot
class MSOfficeDoc extends Office {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/application\/msword/';
	}
}
