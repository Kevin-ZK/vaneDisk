<?php

namespace OC\Search\Result;

/**
 * A found audio file
 */
class Audio extends File {

	/**
	 * Type name; translated in templates
	 * @var string 
	 */
	public $type = 'audio';
	
	/**
	 * @TODO add ID3 information
	 */
}
