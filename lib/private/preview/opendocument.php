<?php
namespace OC\Preview;

//.odt, .ott, .oth, .odm, .odg, .otg, .odp, .otp, .ods, .ots, .odc, .odf, .odb, .odi, .oxt
class OpenDocument extends Office {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/application\/vnd.oasis.opendocument.*/';
	}
}
