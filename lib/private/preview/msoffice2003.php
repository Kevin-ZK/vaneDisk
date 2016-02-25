<?php
namespace OC\Preview;

//.docm, .dotm, .xls(m), .xlt(m), .xla(m), .ppt(m), .pot(m), .pps(m), .ppa(m)
class MSOffice2003 extends Office {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/application\/vnd.ms-.*/';
	}
}
