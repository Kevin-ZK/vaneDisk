<?php

namespace OC\Search\Result;

/**
 * A found image file
 */
class Image extends File {

	/**
	 * Type name; translated in templates
	 * @var string 
	 */
	public $type = 'image';
	
	/**
	 * @TODO add EXIF information
	 */
}
