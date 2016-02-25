<?php
namespace OC\Preview;

//.sxw, .stw, .sxc, .stc, .sxd, .std, .sxi, .sti, .sxg, .sxm
class StarOffice extends Office {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/application\/vnd.sun.xml.*/';
	}
}
