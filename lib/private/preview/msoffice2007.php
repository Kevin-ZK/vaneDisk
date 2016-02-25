<?php
namespace OC\Preview;

//.docx, .dotx, .xlsx, .xltx, .pptx, .potx, .ppsx
class MSOffice2007 extends Office {
	/**
	 * {@inheritDoc}
	 */
	public function getMimeType() {
		return '/application\/vnd.openxmlformats-officedocument.*/';
	}
}
